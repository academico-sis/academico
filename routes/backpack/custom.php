<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group(
    [
        'namespace'  => '\App\Http\Controllers',
        'middleware' => ['web', 'loggedin', 'language'],
        'prefix'     => config('backpack.base.route_prefix'),
    ],
    function () {
        // Registration Routes...

        Route::crud('result', 'Admin\ResultCrudController');
        Route::crud('student', 'Admin\StudentCrudController');
        Route::crud('course', 'Admin\CourseCrudController');
        Route::crud('externalcourse', 'Admin\ExternalCourseCrudController');
        Route::crud('comment', 'Admin\CommentCrudController');

        Route::post('edit-account-info', 'Auth\MyAccountController@postAccountInfoForm');
        Route::post('edit-student-info', 'Auth\MyAccountController@postStudentInfoForm');
        Route::post('edit-profession', 'Auth\MyAccountController@postAccountProfessionForm');
        Route::post('edit-phone', 'Auth\MyAccountController@postPhoneForm');
        Route::post('edit-photo', 'Auth\MyAccountController@postPhotoForm');
        Route::post('edit-contacts', 'Auth\MyAccountController@postContactsForm');
    }
);

// move to routes/web.php
Route::group(
    [
        'namespace'  => '\App\Http\Controllers',
        'middleware' => ['web', 'loggedin', 'language', 'forceupdate'],
        'prefix'     => config('backpack.base.route_prefix'),
    ],
    function () {
        // route numbers match the DB forceupdate field
        Route::get('edit/1', 'Auth\MyAccountController@getAccountInfoForm')->name('backpack.account.info');
        Route::get('edit/2', 'Auth\MyAccountController@getStudentInfoForm')->name('backpack.student.info');
        Route::get('edit/3', 'Auth\MyAccountController@getPhoneForm')->name('backpack.account.phone');
        Route::get('edit/4', 'Auth\MyAccountController@getAccountProfessionForm')->name('backpack.account.profession');
        Route::get('edit/5', 'Auth\MyAccountController@getPhotoForm')->name('backpack.account.photo');
        Route::get('edit/6', 'Auth\MyAccountController@getContactsForm')->name('backpack.account.contacts');
    }
);

// enrollments and invoicing

Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'permission:enrollments.view', 'language'],
    'namespace'  => 'App\Http\Controllers\Admin',
    ], function () {
        Route::crud('enrollment', 'EnrollmentCrudController');
        Route::crud('availablecourse', 'AvailableCourseCrudController');
    }
);

/* Admin routes */
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
        Route::crud('user', 'UserCrudController');
        Route::crud('teacher', 'TeacherCrudController');
        Route::crud('evaluationtype', 'EvaluationTypeCrudController');
        Route::crud('gradetype', 'GradeTypeCrudController');
        Route::crud('skill', 'SkillCrudController');
        Route::post('skill/bulk-attach', 'SkillCrudController@bulkAttachToCourse');
        Route::crud('skilltype', 'SkillTypeCrudController');
        Route::crud('skillscale', 'SkillScaleCrudController');
        Route::crud('resulttype', 'ResultTypeCrudController');
        Route::crud('remoteevent', 'RemoteEventCrudController');
        Route::crud('leave', 'LeaveCrudController');
        Route::crud('leadtype', 'LeadTypeCrudController');
        Route::crud('config', 'ConfigCrudController');
        Route::crud('book', 'BookCrudController');
        Route::crud('fee', 'FeeCrudController');
        Route::crud('discount', 'DiscountCrudController');
        Route::crud('coupon', 'CouponCrudController');
        Route::crud('paymentmethod', 'PaymentmethodCrudController');
        Route::crud('courseevaluation', 'CourseEvaluationCrudController');
    }); // this should be the absolute last line of this file
