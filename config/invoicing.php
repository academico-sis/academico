<?php

use App\Services\Ecuasolutions;
use App\Services\InternalInvoicingService;
use App\Services\SepaInvoicingService;

return [
    'accounting_enabled' => env('ACCOUNTING_ENABLED'),

    'invoicing_system' => env('INVOICING_SYSTEM', 'internal'),

    'internal' => [
        'class' => InternalInvoicingService::class,
    ],

    'sepa' => [
        'class' => SepaInvoicingService::class,
    ],

    'ecuasolutions' => [
        'class' => Ecuasolutions::class,
        'url' => env('ECUASOLUTIONS_URL'),
        'key' => env('ECUASOLUTIONS_KEY'),
    ],

    'allow_scheduled_payments' => env('ALLOW_SCHEDULED_PAYMENTS', false),

    'invoice_numbering' => env('INVOICE_NUMBERING', 'auto'),
];
