<?php

/*
 * Institution settings. These are defaults: the values saved in the Settings area
 * override them (see App\Services\Settings).
 */
return [
    'company_name' => 'Academico',
    'company_address' => null,
    'company_phone' => null,
    'company_id' => null,
    'company_email' => null,

    /*
     * Path of the institution logo on the public disk.
     */
    'logo' => null,

    'calendar_start' => '06:00:00',

    'currency_symbol' => '€',
    'currency_code' => 'EUR',
    'currency_position' => 'after',

    'send_emails_for_absences' => false,

    'send_emails_for_results' => false,

    'include_takings_in_reports' => false,
];
