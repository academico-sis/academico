<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

/* These routes are protected in the controller */
Route::namespace('\App\Http\Controllers')->middleware('web', 'loggedin', 'language')->prefix(config('backpack.base.route_prefix'))->group(function () {
        Route::crud('result', 'Admin\ResultCrudController');
        Route::crud('student', 'Admin\StudentCrudController');
        Route::crud('course', 'Admin\CourseCrudController');
        Route::crud('externalcourse', 'Admin\ExternalCourseCrudController');
        Route::crud('comment', 'Admin\CommentCrudController');
    }
);

/* enrollments and invoicing */

Route::prefix(config('backpack.base.route_prefix'))->middleware('web', 'permission:enrollments.view', 'language')->namespace('App\Http\Controllers\Admin')->group(function () {
        Route::crud('enrollment', 'EnrollmentCrudController');
    }
);

/* CRUD routes accessible to admins or secretary */
Route::prefix(config('backpack.base.route_prefix'))->middleware('web', 'role:admin|secretary', 'language')->namespace('App\Http\Controllers\Admin')->group(function () {
    Route::crud('fee', 'FeeCrudController');
    Route::crud('discount', 'DiscountCrudController');
    Route::crud('coupon', 'CouponCrudController');
    Route::crud('paymentmethod', 'PaymentmethodCrudController');
    Route::crud('institution', 'InstitutionCrudController');
    Route::crud('scholarship', 'ScholarshipCrudController');
});

/* Admin routes - Backpack's CRUD panels, accessible only to administrators */

Route::prefix(config('backpack.base.route_prefix'))->middleware('web', 'role:admin', 'language')->namespace('App\Http\Controllers\Admin')->group(function () {
    Route::crud('period', 'PeriodCrudController');
    Route::crud('event', 'EventCrudController');
    Route::crud('level', 'LevelCrudController');
    Route::crud('room', 'RoomCrudController');
    Route::crud('rhythm', 'RhythmCrudController');
    Route::crud('year', 'YearCrudController');
    Route::crud('campus', 'CampusCrudController');
    Route::crud('teacher', 'TeacherCrudController');
    Route::crud('user', 'UserCrudController');
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
    Route::crud('member', 'MemberCrudController');
    Route::crud('partner', 'PartnerCrudController');
    Route::crud('schedulepreset', 'SchedulePresetCrudController');
    Route::crud('payment', 'PaymentCrudController');
    Route::crud('tax', 'TaxCrudController');
    Route::crud('invoice', 'InvoiceCrudController');
    Route::crud('config', 'ConfigCrudController');
    Route::crud('profession', 'ProfessionCrudController');
    Route::crud('scheduled-payment', 'ScheduledPaymentCrudController');
}); // this should be the absolute last line of this file
