<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

/* Route::group(
    [
        'namespace'  => 'Backpack\Base\app\Http\Controllers',
        'middleware' => 'web',
        'prefix'     => config('backpack.base.route_prefix'),
    ],
    function () {
        // if not otherwise configured, setup the auth routes
        if (config('backpack.base.setup_auth_routes')) {

            // Password Reset Routes...
            Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('backpack.auth.password.reset');
            Route::post('password/reset', 'Auth\ResetPasswordController@reset');
            Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('backpack.auth.password.reset.token');
            Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('backpack.auth.password.email');
        }

    });
 */
    
Route::group(
    [
        'namespace'  => '\App\Http\Controllers',
        'middleware' => ['web', 'language'],
        'prefix'     => config('backpack.base.route_prefix'),
    ],
    function () {
        // Registration Routes...
        Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('backpack.auth.register');
        Route::post('register', 'Auth\RegisterController@register');
        
        // if not otherwise configured, setup the "my account" routes
        Route::get('edit-account-info', 'Auth\MyAccountController@getAccountInfoForm')->name('backpack.account.info');
        Route::get('edit-student-info', 'Auth\MyAccountController@getStudentInfoForm')->name('backpack.student.info');
        Route::get('edit-account-phone', 'Auth\MyAccountController@getPhoneForm')->name('backpack.account.phone');
        Route::get('edit-account-profession', 'Auth\MyAccountController@getAccountProfessionForm')->name('backpack.account.profession');
        Route::get('edit-account-photo', 'Auth\MyAccountController@getPhotoForm')->name('backpack.account.photo');
        Route::get('edit-account-contacts', 'Auth\MyAccountController@getAccountContactsForm')->name('backpack.account.contacts');
        
        Route::post('edit-account-info', 'Auth\MyAccountController@postAccountInfoForm');
        Route::post('edit-student-info', 'Auth\MyAccountController@postStudentInfoForm');

        CRUD::resource('result', 'Admin\ResultCrudController');
        CRUD::resource('contact', 'Admin\ContactCrudController');

        CRUD::resource('preinvoice', 'Admin\PreInvoiceCrudController');


        });


// course management
Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'permission:courses.view', 'language'],
    'namespace'  => 'App\Http\Controllers\Admin',
    ], function () {
        CRUD::resource('course', 'CourseCrudController');
});

Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'permission:evaluation.view', 'language'],
    'namespace'  => 'App\Http\Controllers\Admin',
    ], function () {
        CRUD::resource('courseskill', 'CourseSkillCrudController');

});


// students and enrollments management
Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'permission:enrollments.view', 'language'],
    'namespace'  => 'App\Http\Controllers\Admin',
    ], function () {
        CRUD::resource('student', 'StudentCrudController');
        CRUD::resource('enrollment', 'EnrollmentCrudController');
        CRUD::resource('comment', 'CommentCrudController');
        //CRUD::resource('preinvoice', 'PreInvoiceCrudController')->middleware('permission:invoice.view');
        CRUD::resource('availablecourse', 'AvailableCourseCrudController');
});

/* Admin routes */
Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'role:admin', 'language'],
    'namespace'  => 'App\Http\Controllers\Admin',
    ], function () {
        CRUD::resource('period', 'PeriodCrudController');
        CRUD::resource('event', 'EventCrudController');
        CRUD::resource('level', 'LevelCrudController');
        CRUD::resource('room', 'RoomCrudController');
        CRUD::resource('rhythm', 'RhythmCrudController');
        CRUD::resource('year', 'YearCrudController');
        CRUD::resource('campus', 'CampusCrudController');
        CRUD::resource('user', 'UserCrudController');
        CRUD::resource('teacher', 'TeacherCrudController');
        CRUD::resource('evaluationtype', 'EvaluationTypeCrudController');
        CRUD::resource('gradetype', 'GradeTypeCrudController');
        CRUD::resource('skill', 'SkillCrudController')->with(function() {
            Route::post('skill/bulk-attach', 'SkillCrudController@bulkAttachToCourse');
      });
        CRUD::resource('skilltype', 'SkillTypeCrudController');
        CRUD::resource('skillscale', 'SkillScaleCrudController');
        CRUD::resource('resulttype', 'ResultTypeCrudController');
        CRUD::resource('remoteevent', 'RemoteEventCrudController');
        CRUD::resource('leave', 'LeaveCrudController');
        CRUD::resource('external', 'ExternalCourseCrudController');
    CRUD::resource('leadtype', 'LeadTypeCrudController');
}); // this should be the absolute last line of this file