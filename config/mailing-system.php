<?php


return [
    'external_mailing_enabled' => env('EXTERNAL_MAILING_ENABLED', false),

    'mailing_system' => env('MAILING_SYSTEM', 'log'),

    'log' => [
        'class' => \App\Services\LogMailingSystem::class,
    ],

    'mailerlite' => [
        'class' => \App\Services\MailerliteService::class,
        'key' => env('MAILERLITE_API_KEY'),
        'activeStudentsListId' => env('ACTIVE_STUDENTS_MAILING_LIST_ID'),
        'inactiveStudentsListId' => env('INACTIVE_STUDENTS_MAILING_LIST_ID'),
    ],
];
