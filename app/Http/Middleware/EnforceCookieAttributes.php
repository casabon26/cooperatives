<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class EnforceCookieAttributes
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $cookies = $response->headers->getCookies();
        if (empty($cookies)) {
            return $response;
        }

        // Remove existing Set-Cookie headers
        $response->headers->remove('Set-Cookie');

        foreach ($cookies as $cookie) {
            // Preserve original values but enforce secure/httpOnly/SameSite
            $name = $cookie->getName();
            $value = $cookie->getValue();
            $expires = $cookie->getExpiresTime();
            $path = $cookie->getPath();
            $domain = $cookie->getDomain() ?: null;
            $secure = true; // enforce secure
            $httpOnly = true; // enforce httpOnly
            // attempt to preserve SameSite if available, otherwise default to Lax
            $sameSite = method_exists($cookie, 'getSameSite') ? $cookie->getSameSite() : 'Lax';
            if (!$sameSite) {
                $sameSite = 'Lax';
            }

            $new = new SymfonyCookie($name, $value, $expires, $path, $domain, $secure, $httpOnly, false, $sameSite);
            $response->headers->setCookie($new);
        }

        return $response;
    }
}
