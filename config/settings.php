<?php

/*
 * Institution settings. These are defaults: the values saved in the Settings area
 * override them (see App\Services\Settings).
 */
return [
    'manager_email' => null,
    'reports_email' => null,
    'secretary_email' => null,
    'courses_timezone' => 'UTC',
    'partnership_alerts' => false,
    'external_courses_enabled' => false,
    'external_courses_report' => false,
    'monthly_report' => false,
    'teachers_can_edit_result' => false,
    'attendance_reports_enabled' => false,
    'teachers_can_view_calendars' => false,
    'collect_student_banking_info' => false,
    'members_enabled' => false,
];
