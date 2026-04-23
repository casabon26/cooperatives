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
        // Use the Origin header when present to echo back, otherwise fallback to '*'
        $origin = $request->headers->get('Origin') ?: '*';

        // If this is a preflight request, respond immediately with appropriate headers
        if ($request->getMethod() === 'OPTIONS') {
            $headers = [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => $request->header('Access-Control-Request-Headers') ?: 'Content-Type, X-Requested-With, X-CSRF-TOKEN, Authorization',
                'Access-Control-Allow-Credentials' => 'true',
            ];
            return response()->json(null, 204, $headers);
        }

        $response = $next($request);

        // Attach CORS headers to the response if not already present
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers') ?: 'Content-Type, X-Requested-With, X-CSRF-TOKEN, Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
