<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EncryptCookies
{
    public function handle(Request $request, Closure $next)
    {
        // Minimal stub — production should use the framework implementation
        return $next($request);
    }
}
