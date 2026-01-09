<?php

namespace Fratac\LaravelDistributedKv\Http\Controllers;

use Fratac\LaravelDistributedKv\Events\ClusterClientAdded;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Fratac\LaravelDistributedKv\Models\DistributedKvEntry;
use Illuminate\Support\Facades\Cache;

class SyncController extends Controller
{
    public function pull(Request $request)
    {
        $since = $request->query('since');

        $query = DistributedKvEntry::query();

        if ($since) {
            $query->where('updated_at', '>', $since);
        }

        $items = $query->get();

        return response()->json([
            'data' => $items,
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function push(Request $request)
    {
        $data = $request->input('data', []);

        foreach ($data as $item) {
            $this->storeOrUpdateEntry($item);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function storeOrUpdateEntry(array $item): void
    {
        $key = $item['key'] ?? null;
        $value = $item['value'] ?? null;
        $updatedAt = $item['updated_at'] ?? null;

        if (! $key || ! $updatedAt) {
            return;
        }

        $remoteUpdatedAt = \Carbon\Carbon::parse($updatedAt);

        $existing = DistributedKvEntry::find($key);

        if ($existing) {
            if ($remoteUpdatedAt->gt($existing->updated_at)) {
                $existing->update([
                    'value' => $value,
                    'updated_at' => $remoteUpdatedAt,
                ]);
            }
        } else {
            DistributedKvEntry::create([
                'key' => $key,
                'value' => $value,
                'updated_at' => $remoteUpdatedAt,
            ]);
        }
    }

    public function registerClient(Request $request)
    {
        $name = $request->input('name');
        $url  = $request->input('url');

        if (! $name || ! $url) {
            return response()->json(['error' => 'Invalid client data'], 422);
        }

        // Lista client locale salvata in cache (o DB, se vuoi)
        $clients = Cache::get('distributed_kv_clients', []);
        event(new ClusterClientAdded($name, $url));

        $clients[$name] = $url;

        Cache::forever('distributed_kv_clients', $clients);

        return response()->json(['status' => 'ok']);
    }
}
