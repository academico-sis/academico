<?php

/*
 * Institution settings. These are defaults: the values saved in the Settings area
 * override them (see App\Services\Settings).
 */
return [
    'accounting_enabled' => false,

    'allow_scheduled_payments' => false,

    /*
     * 'auto' numbers invoices per type and year, 'manual' uses the receipt number entered at checkout.
     */
    'invoice_numbering' => 'auto',

    'allow_empty_payment_methods' => false,

    'adding_scholarship_marks_as_paid' => false,

    'price_categories_enabled' => false,

    'skip_invoice_data_step' => false,

    'add_books_to_invoices' => true,

    'invoices_contain_enrollments_only' => false,

    'invoice_footer' => null,
];
