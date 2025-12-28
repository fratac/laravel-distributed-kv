<?php

namespace Fratac\LaravelDistributedKv;

use Illuminate\Support\ServiceProvider;
use Fratac\LaravelDistributedKv\Services\SyncManager;

class LaravelDistributedKvServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/laravel-distributed-kv.php',
            'laravel-distributed-kv'
        );

        $this->app->singleton(SyncManager::class, function ($app) {
            return new SyncManager(
                config('laravel-distributed-kv.clients', []),
                config('laravel-distributed-kv.auth_token'),
                config('laravel-distributed-kv.client_name')
            );
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/laravel-distributed-kv.php' => config_path('laravel-distributed-kv.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Fratac\LaravelDistributedKv\Console\Commands\LaravelDistributeKvCommand::class,
            ]);
        }
    }
}
