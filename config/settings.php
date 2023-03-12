<?php

return [
    'app_url' => env('APP_URL'),
    'manager_email' => env('MANAGER_EMAIL'),
    'reports_email' => env('REPORTS_EMAIL'),
    'secretary_email' => env('SECRETARY_EMAIL'),
    'courses_timezone' => env('COURSES_TIMEZONE'),
    'mail_from_name' => env('MAIL_FROM_NAME'),
    'sentry_laravel_dsn' => env('SENTRY_LARAVEL_DSN'),
    'partnership_alerts' => env('PARTNERSHIP_ALERTS', false),
    'external_courses_enabled' => env('EXTERNAL_COURSES_ENABLED', false),
    'external_courses_report' => env('EXTERNAL_COURSES_REPORT', false),
    'monthly_report' => env('MONTHLY_REPORT', false),
    'teachers_can_edit_result' => env('TEACHERS_CAN_EDIT_RESULT', false),
    'attendance_reports_enabled' => env('ATTENDANCE_REPORTS_ENABLED', false),
];
