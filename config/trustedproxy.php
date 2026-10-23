<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | The reverse proxies or load balancers whose X-Forwarded-* headers are
    | trusted, to know the client IP address and whether HTTPS is used: a
    | comma-separated list of IP addresses or ranges, or "*" to trust any
    | proxy (only if the application cannot be reached without it). Leave
    | empty when clients reach the application directly.
    |
    */

    'proxies' => env('TRUSTED_PROXIES'),

];
