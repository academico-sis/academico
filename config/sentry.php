<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN'),

    // capture release as git sha
    // 'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),

    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,

    'send_default_pii' => true,

];
