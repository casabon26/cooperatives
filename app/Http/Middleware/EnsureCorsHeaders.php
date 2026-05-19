<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCorsHeaders
{
    /**
     * Handle an incoming request and ensure CORS response headers are present.
     */
    public function handle(Request $request, Closure $next)
    {
        // Allowlist origins via env variable (comma-separated). If empty, default to no-origin (safe).
        $allowed = array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', ''))));
        $origin = $request->headers->get('Origin');

        // If preflight, respond with allowed headers only when origin is allowed
        if ($request->getMethod() === 'OPTIONS') {
            if ($origin && in_array($origin, $allowed)) {
                $headers = [
                    'Access-Control-Allow-Origin' => $origin,
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => $request->header('Access-Control-Request-Headers') ?: 'Content-Type, X-Requested-With, X-CSRF-TOKEN, Authorization',
                    'Access-Control-Allow-Credentials' => 'true',
                ];
                return response()->json(null, 204, $headers);
            }
            return response()->json(null, 204);
        }

        $response = $next($request);

        if ($origin && in_array($origin, $allowed)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers') ?: 'Content-Type, X-Requested-With, X-CSRF-TOKEN, Authorization');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
