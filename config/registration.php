<?php

return [
    'picture' => [
        'enabled' => env('REGISTER_WITH_PICTURE_ALLOWED', true),
        'mandatory' => env('REGISTER_WITH_PICTURE_MANDATORY', false),
    ],

    'ensure_email_unicity' => env('ENSURE_EMAIL_UNICITY', false),

    'terms_url' => env('TERMS_URL'),
    'rules_url' => env('RULES_URL'),
];
