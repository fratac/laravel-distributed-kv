<?php

namespace Fratac\LaravelDistributedKv\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyKvToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-DKV-TOKEN');

        if (! $token || $token !== config('laravel-distributed-kv.auth_token')) {
            abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}
