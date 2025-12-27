<?php

namespace Fratac\LaravelDistributedKv\Services;

use Carbon\Carbon;
use Fratac\LaravelDistributedKv\Events\ClusterClientAdded;
use Fratac\LaravelDistributedKv\Events\KeyDeleted;
use Fratac\LaravelDistributedKv\Models\DistributedKvEntry;
use Fratac\LaravelDistributedKv\Events\KeyCreated;
use Fratac\LaravelDistributedKv\Events\KeyUpdated;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

//class SyncManager
class SyncManager
{
    protected array $configClients;
    protected ?string $token;
    protected string $clientName;

    public function __construct(array $clients, ?string $token, string $clientName)
    {
        $this->configClients = $clients;
        $this->token = $token;
        $this->clientName = $clientName;
    }

    public function getClients(): array
    {
        $dynamic = Cache::get('distributed_kv_clients', []);
        return array_merge($this->configClients, $dynamic);
    }

    public function sync(): void
    {
        $clients = $this->getClients();

        $lastSync = Cache::get('distributed_kv_last_sync');

        $now = now();

        // Pull da tutti i client
        foreach ($clients as $name => $url) {
            if ($name === $this->clientName) {
                continue;
            }

            $this->pullFromClient($url, $lastSync, $name);
        }

        // Push verso tutti i client
        foreach ($clients as $name => $url) {
            if ($name === $this->clientName) {
                continue;
            }

            $this->pushToClient($url, $lastSync);
        }

        Cache::forever('distributed_kv_last_sync', $now->toISOString());
    }

    protected function pullFromClient(string $baseUrl, ?string $since = null, string $clientName = 'remote'): void
    {
        $params = [];
        if ($since) {
            $params['since'] = $since;
        }

        $response = Http::withHeaders([
            'X-DKV-TOKEN' => $this->token,
        ])->get(rtrim($baseUrl, '/') . config('laravel-distributed-kv.base_path') . '/pull', $params);

        if (!$response->successful()) {
            return;
        }

        $payload = $response->json();

        foreach ($payload['data'] ?? [] as $item) {
            $this->storeRemoteEntry($item, $clientName);
        }
    }

    protected function storeRemoteEntry(array $item, string $sourceClient = 'remote'): void
    {
        $key = $item['key'] ?? null;
        $value = $item['value'] ?? null;
        $version = $item['version'] ?? 1;
        $updatedAt = $item['updated_at'] ?? null;
        $deletedAt = $item['deleted_at'] ?? null;

        if (! $key || ! $updatedAt) {
            return;
        }

        $remoteUpdatedAt = Carbon::parse($updatedAt);
        $remoteDeletedAt = $deletedAt ? Carbon::parse($deletedAt) : null;

        $existing = DistributedKvEntry::find($key);

        if ($existing) {
            $localVersion = $existing->version;
            $localUpdatedAt = $existing->updated_at;

            // Strategia: prima versione, poi timestamp
            $shouldUpdate = $version > $localVersion ||
                ($version === $localVersion && $remoteUpdatedAt->gt($localUpdatedAt));

            if (! $shouldUpdate) {
                return;
            }

            $oldValue = $existing->value;

            $existing->update([
                'value' => $value,
                'version' => $version,
                'updated_at' => $remoteUpdatedAt,
                'deleted_at' => $remoteDeletedAt,
            ]);

            if ($remoteDeletedAt) {
                event(new KeyDeleted($key, $oldValue));
            } else {
                event(new KeyUpdated($key, $oldValue, $value));
            }

            event(new KeySynced($key, $oldValue, $value, $sourceClient, 'pull'));
        } else {
            $entry = DistributedKvEntry::create([
                'key' => $key,
                'value' => $value,
                'version' => $version,
                'updated_at' => $remoteUpdatedAt,
                'deleted_at' => $remoteDeletedAt,
            ]);

            if ($remoteDeletedAt) {
                event(new KeyDeleted($key, null));
            } else {
                event(new KeyCreated($key, $value));
            }

            event(new KeySynced($key, null, $value, $sourceClient, 'pull'));
        }
    }



    protected function pushToClient(string $baseUrl, ?string $since = null): void
    {
        $query = DistributedKvEntry::query();

        if ($since) {
            $query->where('updated_at', '>', $since);
        }

        $items = $query->get()->map(function ($entry) {
            return [
                'key' => $entry->key,
                'value' => $entry->value,
                'version' => $entry->version,
                'updated_at' => $entry->updated_at->toISOString(),
                'deleted_at' => optional($entry->deleted_at)->toISOString(),];
        })->values()->all();

        if (empty($items)) {
            return;
        }

        Http::withHeaders([
            'X-DKV-TOKEN' => $this->token,
        ])->post(rtrim($baseUrl, '/') . config('laravel-distributed-kv.base_path') . '/push', [
            'data' => $items,
        ]);
    }

    public function registerNewClient(string $name, string $url): void
    {
        // Salva localmente
        $clients = Cache::get('distributed_kv_clients', []);
        $clients[$name] = $url;
        Cache::forever('distributed_kv_clients', $clients);

        event(new ClusterClientAdded($name, $url));

        // Propaga agli altri
        foreach ($this->getClients() as $clientName => $clientUrl) {
            if ($clientName === $this->clientName) {
                continue;
            }

            Http::withHeaders([
                'X-DKV-TOKEN' => $this->token,
            ])->post(rtrim($clientUrl, '/') . config('laravel-distributed-kv.base_path') . '/register-client', [
                'name' => $name,
                'url' => $url,
            ]);
        }
    }

    public function set(string $key, $value): void
    {
        $entry = DistributedKvEntry::find($key);

        $encoded = is_scalar($value) ? (string) $value : json_encode($value);
        $now = now();

        if ($entry) {
            $oldValue = $entry->value;

            $entry->update([
                'value' => $encoded,
                'version' => $entry->version + 1,
                'deleted_at' => null,
                'updated_at' => $now,
            ]);

            event(new KeyUpdated($key, $oldValue, $encoded));
        } else {
            DistributedKvEntry::create([
                'key' => $key,
                'value' => $encoded,
                'version' => 1,
                'deleted_at' => null,
                'updated_at' => $now,
            ]);

            event(new KeyCreated($key, $encoded));
        }
    }


    public function get(string $key, $default = null)
    {
        $entry = DistributedKvEntry::find($key);
        if (!$entry) {
            return $default;
        }

        $value = $entry->value;

        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }


    public function delete(string $key): void
    {
        $entry = DistributedKvEntry::find($key);

        if (!$entry || $entry->isDeleted()) {
            return;
        }

        $oldValue = $entry->value;

        $entry->update([
            'value' => null,
            'version' => $entry->version + 1,
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        event(new KeyDeleted($key, $oldValue));
    }


}
