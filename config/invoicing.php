<?php

use App\Services\Ecuasolutions;
use App\Services\InternalInvoicingService;

return [
    'accounting_enabled' => env('ACCOUNTING_ENABLED'),

    'invoicing_system' => env('INVOICING_SYSTEM', 'internal'),

    'internal' => [
        'class' => InternalInvoicingService::class,
    ],

    'ecuasolutions' => [
        'class' => Ecuasolutions::class,
        'url' => env('ECUASOLUTIONS_URL'),
        'key' => env('ECUASOLUTIONS_KEY'),
    ],
];
