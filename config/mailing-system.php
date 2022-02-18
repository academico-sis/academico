<?php

use App\Services\LogMailingSystem;
use App\Services\MailerliteService;

return [
    'external_mailing_enabled' => env('EXTERNAL_MAILING_ENABLED', false),

    'mailing_system' => env('MAILING_SYSTEM', 'log'),

    'log' => [
        'class' => LogMailingSystem::class,
    ],

    'mailerlite' => [
        'class' => MailerliteService::class,
        'key' => env('MAILERLITE_API_KEY'),
        'activeStudentsListId' => env('ACTIVE_STUDENTS_MAILING_LIST_ID'),
        'inactiveStudentsListId' => env('INACTIVE_STUDENTS_MAILING_LIST_ID'),
    ],
];
