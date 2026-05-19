<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as FideloperTrustProxies;

class TrustProxies extends FideloperTrustProxies
{
    /**
     * The trusted proxies for this application.
     * Use '*' to trust all proxies when behind a load balancer configured correctly.
     * In production, prefer specific proxy IPs.
     *
     * @var array|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
