<?php

return [
    'picture' => [
        'enabled' => env('REGISTER_WITH_PICTURE_ALLOWED', true),
        'mandatory' => env('REGISTER_WITH_PICTURE_MANDATORY', false),
    ]
];
