<?php

use App\Services\AFLojaCertificatesService;
use App\Services\GenericCertificatesService;

return [
    'style' => env('CERTIFICATES_STYLE', 'none'),

    'supported' => match(env('CERTIFICATES_STYLE')) {
        'afloja' => true,
        default => false,
    },

    'none' => [
        'class' => GenericCertificatesService::class,
    ],

    'afloja' => [
        'class' => AFLojaCertificatesService::class,
    ],
];
