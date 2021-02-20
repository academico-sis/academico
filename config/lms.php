<?php

use App\Services\ApolearnService;
use App\Services\LMSLogService;

return [
    'sync_to' => env('LMS_SYNC_TO', 'log'),

    'log' => [
        'class' => LMSLogService::class,
    ],

    'apolearn' => [
        'class' => ApolearnService::class,
        'url' => env('APOLEARN_URL'),
        'api_key' => env('APOLEARN_KEY'),
        'username' => env('APOLEARN_USERNAME'),
        'password' => env('APOLEARN_PASSWORD'),
        'admin_user_id' => env('APOLEARN_ADMIN_USER_ID'),
        'default_category_id' => env('APOLEARN_DEFAULT_CATEGORY_ID'),
        'default_level_id' => env('APOLEARN_DEFAULT_LEVEL_ID'),
    ],
];
