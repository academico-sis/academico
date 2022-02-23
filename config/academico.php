<?php

return [
    'company_name' => env('COMPANY_NAME', 'Academico'),
    'company_address' => env('COMPANY_ADDRESS', ''),
    'company_phone' => env('COMPANY_PHONE', ''),
    'company_id' => env('COMPANY_ID', ''),
    'company_email' => env('COMPANY_EMAIL', ''),

    'calendar_start' => env('DAY_START_IN_CALENDARS', '06:00:00'),

    /*
 * available languages
 */

    'languages' => ['fr', 'en', 'es'],

    'currency_symbol' => env('CURRENCY_SYMBOL', '€'),
    'currency_code' => env('CURRENCY_CODE', 'EUR'),
    'currency_position' => env('CURRENCY_POSITION', 'after'),

    'books_module' => env('BOOKS_MODULE_ENABLED', false),

    'send_emails_for_absences' => env('SEND_ABSENCE_NOTIFICATIONS', false),

    'send_emails_for_results' => env('SEND_RESULTS_NOTIFICATIONS', false),

    'include_takings_in_reports' => env('INCLUDE_TAKINGS_IN_REPORTS', false),

    'admin_email' => env('ADMIN_EMAIL'),
];
