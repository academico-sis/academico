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

    'allow_empty_payment_methods' => env('ALLOW_EMPTY_PAYMENT_METHODS', false),

    'allowed_blank_fields' => explode(',', env('ALLOWED_EMPTY_INVOICE_FIELDS', '')),

    'adding_scholarship_marks_as_paid' => env('ADDING_SCHOLARSHIPS_MARKS_AS_PAID', false),

    'price_categories_enabled' => env('PRICE_GROUPS_ENABLED', false),

    'skip_invoice_data_step' => env('SKIP_INVOICE_DATA_STEP', false),

    'add_books_to_invoices' => env('CHECKOUT_BOOKS_WITH_ENROLLMENTS', true),

];
