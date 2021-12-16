<?php

use App\Services\AFLojaCertificatesService;
use App\Services\GenericCertificatesService;

return [
    'style' => env('CERTIFICATES_STYLE', 'none'),

    'none' => [
        'class' => GenericCertificatesService::class,
    ],

    'afloja' => [
        'class' => AFLojaCertificatesService::class,
    ],
];
