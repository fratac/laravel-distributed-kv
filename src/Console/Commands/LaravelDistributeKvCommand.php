<?php

namespace Fratac\LaravelDistributedKv\Console\Commands;

use Illuminate\Console\Command;
use Fratac\LaravelDistributedKv\Services\SyncManager;

class SyncCommand extends Command
{
    protected $signature = 'dkv:sync';
    protected $description = 'Sync distributed key-value data with other Laravel clients';

    public function handle(SyncManager $syncManager)
    {
        $this->info('Starting distributed DKV sync...');
        $syncManager->sync();
        $this->info('Distributed DKV sync completed.');

        return 0;
    }
}
