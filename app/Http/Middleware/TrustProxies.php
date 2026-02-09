<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrustProxies
{
    public function handle(Request $request, Closure $next)
    {
        // Minimal stub for proxy trust; in production use framework's TrustProxies
        return $next($request);
    }
}
