<?php

namespace Tests\Feature;

use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Tests\TestCase;

class TrustedProxiesTest extends TestCase
{
    private function clientIp(): string
    {
        $request = Request::create('/', server: ['REMOTE_ADDR' => '10.0.0.1', 'HTTP_X_FORWARDED_FOR' => '203.0.113.7']);

        return $this->app->make(TrustProxies::class)
            ->handle($request, fn (Request $request) => response($request->ip()))
            ->getContent();
    }

    public function test_forwarded_headers_are_ignored_by_default(): void
    {
        config(['trustedproxy.proxies' => null]);

        $this->assertSame('10.0.0.1', $this->clientIp());
    }

    public function test_forwarded_headers_of_a_configured_proxy_are_trusted(): void
    {
        config(['trustedproxy.proxies' => '10.0.0.1, 10.0.0.2']);
        $this->assertSame('203.0.113.7', $this->clientIp());

        config(['trustedproxy.proxies' => '192.168.0.0/16']);
        $this->assertSame('10.0.0.1', $this->clientIp());
    }
}
