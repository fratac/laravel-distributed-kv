<?php

use Illuminate\Support\Facades\Route;
use Fratac\LaravelDistributedKv\Http\Controllers\AdminController;

Route::middleware(['web']) // eventualmente aggiungi auth
->prefix('/dkv-admin')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dkv.admin.index');
    });
