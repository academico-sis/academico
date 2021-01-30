<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

/* These routes are protected in the controller */
Route::group(
    [
        'namespace'  => '\App\Http\Controllers',
        'middleware' => ['web', 'loggedin', 'language'],
        'prefix'     => config('backpack.base.route_prefix'),
    ],
    function () {
        Route::crud('result', 'Admin\ResultCrudController');
        Route::crud('student', 'Admin\StudentCrudController');
        Route::crud('course', 'Admin\CourseCrudController');
        Route::crud('comment', 'Admin\CommentCrudController');
    }
);

/* enrollments and invoicing */

Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'permission:enrollments.view', 'language'],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () {
    Route::crud('enrollment', 'EnrollmentCrudController');
}
);

/* CRUD routes accessible to admins or secretary */
Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'role:admin|secretary', 'language'],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () {
    Route::crud('fee', 'FeeCrudController');
    Route::crud('discount', 'DiscountCrudController');
    Route::crud('coupon', 'CouponCrudController');
    Route::crud('paymentmethod', 'PaymentmethodCrudController');
    Route::crud('institution', 'InstitutionCrudController');
});

/* Admin routes - Backpack's CRUD panels, accessible only to administrators */

Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'role:admin', 'language'],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () {
    Route::crud('period', 'PeriodCrudController');
    Route::crud('event', 'EventCrudController');
    Route::crud('level', 'LevelCrudController');
    Route::crud('room', 'RoomCrudController');
    Route::crud('rhythm', 'RhythmCrudController');
    Route::crud('year', 'YearCrudController');
    Route::crud('campus', 'CampusCrudController');
    Route::crud('teacher', 'TeacherCrudController');
    Route::crud('evaluationtype', 'EvaluationTypeCrudController');
    Route::crud('gradetype', 'GradeTypeCrudController');
    Route::crud('skill', 'SkillCrudController');
    Route::crud('skilltype', 'SkillTypeCrudController');
    Route::crud('skillscale', 'SkillScaleCrudController');
    Route::crud('resulttype', 'ResultTypeCrudController');
    Route::crud('remoteevent', 'RemoteEventCrudController');
    Route::crud('leave', 'LeaveCrudController');
    Route::crud('leadtype', 'LeadTypeCrudController');
    Route::crud('book', 'BookCrudController');
    Route::crud('gradetypecategory', 'GradeTypeCategoryCrudController');
}); // this should be the absolute last line of this file
