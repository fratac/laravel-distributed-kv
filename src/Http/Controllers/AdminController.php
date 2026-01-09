<?php

namespace App\LaravelDistributedKv\Http\Controllers;

use Illuminate\Routing\Controller;
use Fratac\LaravelDistributedKv\Models\DistributedKvEntry;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function index()
    {
        $entries = DistributedKvEntry::orderBy('key')->get();

        $clients = Cache::get('distributed_kv_clients', []);
        $configClients = config('laravel-distributed-kv.clients', []);
        $allClients = array_merge($configClients, $clients);

        $lastSync = Cache::get('distributed_kv_last_sync');

        return view('laravel-distributed-kv::admin.index', [
            'entries' => $entries,
            'clients' => $allClients,
            'lastSync' => $lastSync,
            'clientName' => config('laravel-distributed-kv.client_name'),
        ]);
    }
}
