<?php

namespace App\LaravelDistributedKv\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelDistributedKv extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Fratac\LaravelDistributedKv\Services\SyncManager::class;
    }
}
