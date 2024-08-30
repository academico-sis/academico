<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

/* These routes are protected in the controller */
Route::middleware('web', 'loggedin', 'language')->prefix(config('backpack.base.route_prefix'))->group(function () {
    Route::crud('result', 'App\Http\Controllers\Admin\ResultCrudController');
    Route::crud('student', 'App\Http\Controllers\Admin\StudentCrudController');
    Route::crud('course', 'App\Http\Controllers\Admin\CourseCrudController');
    Route::crud('externalcourse', 'App\Http\Controllers\Admin\ExternalCourseCrudController');
}
);

/* enrollments and invoicing */

Route::prefix(config('backpack.base.route_prefix'))->middleware('web', 'permission:enrollments.view', 'language')->group(function () {
    Route::crud('enrollment', 'App\Http\Controllers\Admin\EnrollmentCrudController');
}
);

/* CRUD routes accessible to admins or secretary */
Route::prefix(config('backpack.base.route_prefix'))->middleware('web', 'role:admin|secretary', 'language')->group(function () {
    Route::crud('fee', 'App\Http\Controllers\Admin\FeeCrudController');
    Route::crud('discount', 'App\Http\Controllers\Admin\DiscountCrudController');
    Route::crud('coupon', 'App\Http\Controllers\Admin\CouponCrudController');
    Route::crud('paymentmethod', 'App\Http\Controllers\Admin\PaymentmethodCrudController');
    Route::crud('institution', 'App\Http\Controllers\Admin\InstitutionCrudController');
    Route::crud('scholarship', 'App\Http\Controllers\Admin\ScholarshipCrudController');
});

/* Admin routes - Backpack's CRUD panels, accessible only to administrators */

Route::prefix(config('backpack.base.route_prefix'))->middleware('web', 'role:admin', 'language')->group(function () {
    Route::crud('period', 'App\Http\Controllers\Admin\PeriodCrudController');
    Route::crud('event', 'App\Http\Controllers\Admin\EventCrudController');
    Route::crud('level', 'App\Http\Controllers\Admin\LevelCrudController');
    Route::crud('room', 'App\Http\Controllers\Admin\RoomCrudController');
    Route::crud('rhythm', 'App\Http\Controllers\Admin\RhythmCrudController');
    Route::crud('year', 'App\Http\Controllers\Admin\YearCrudController');
    Route::crud('teacher', 'App\Http\Controllers\Admin\TeacherCrudController');
    Route::crud('evaluationtype', 'App\Http\Controllers\Admin\EvaluationTypeCrudController');
    Route::crud('gradetype', 'App\Http\Controllers\Admin\GradeTypeCrudController');
    Route::crud('skill', 'App\Http\Controllers\Admin\SkillCrudController');
    Route::crud('skilltype', 'App\Http\Controllers\Admin\SkillTypeCrudController');
    Route::crud('skillscale', 'App\Http\Controllers\Admin\SkillScaleCrudController');
    Route::crud('resulttype', 'App\Http\Controllers\Admin\ResultTypeCrudController');
    Route::crud('leave', 'App\Http\Controllers\Admin\LeaveCrudController');
    Route::crud('book', 'App\Http\Controllers\Admin\BookCrudController');
    Route::crud('gradetypecategory', 'App\Http\Controllers\Admin\GradeTypeCategoryCrudController');
    Route::crud('member', 'App\Http\Controllers\Admin\MemberCrudController');
    Route::crud('partner', 'App\Http\Controllers\Admin\PartnerCrudController');
    Route::crud('payment', 'App\Http\Controllers\Admin\PaymentCrudController');
    Route::crud('tax', 'App\Http\Controllers\Admin\TaxCrudController');
    Route::crud('invoice', 'App\Http\Controllers\Admin\InvoiceCrudController');
    Route::crud('profession', 'App\Http\Controllers\Admin\ProfessionCrudController');
    Route::crud('scheduled-payment', 'App\Http\Controllers\Admin\ScheduledPaymentCrudController');
}); // this should be the absolute last line of this file
