<?php

use App\Services\AFSantiagoEnrollmentSheetService;
use App\Services\GenericEnrollmentSheetService;

return [
    'style' => env('ENROLLMENT_SHEET_STYLE', 'none'),

    'supported' => match(env('ENROLLMENT_SHEET_STYLE')) {
        'afsantiago' => true,
        default => false,
    },

    'afsantiago' => [
        'class' => AFSantiagoEnrollmentSheetService::class,
    ],

    'none' => [
        'class' => GenericEnrollmentSheetService::class,
    ]
];
