<?php

use Illuminate\Support\Facades\Route;
use Fratac\LaravelDistributedKv\Http\Controllers\SyncController;
use Fratac\LaravelDistributedKv\Http\Middleware\VerifyKvToken;

Route::prefix(config('laravel-distributed-kv.base_path', '/api/dkv'))
    ->middleware(['api', VerifyKvToken::class])
    ->group(function () {
        Route::get('/pull', [SyncController::class, 'pull']);
        Route::post('/push', [SyncController::class, 'push']);
        Route::post('/register-client', [SyncController::class, 'registerClient']);
    });
