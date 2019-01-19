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
        'middleware' => 'web',
        'prefix'     => config('backpack.base.route_prefix'),
    ],
    function () {
        // Registration Routes...
        Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('backpack.auth.register');
        Route::post('register', 'Auth\RegisterController@register');
        
        // if not otherwise configured, setup the "my account" routes
        Route::get('edit-account-info', 'Auth\MyAccountController@getAccountInfoForm')->name('backpack.account.info');
        Route::post('edit-account-info', 'Auth\MyAccountController@postAccountInfoForm');

        });


// course management
Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'permission:courses.view'],
    'namespace'  => 'App\Http\Controllers\Admin',
    ], function () { // custom admin routes
    CRUD::resource('course', 'CourseCrudController');
});

// students and enrollments management
Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'permission:enrollments.view'],
    'namespace'  => 'App\Http\Controllers\Admin',
    ], function () { // custom admin routes
    CRUD::resource('student', 'StudentCrudController');
    CRUD::resource('enrollment', 'EnrollmentCrudController');
    CRUD::resource('userdata', 'UserDataCrudController');
    CRUD::resource('comment', 'CommentCrudController');
    CRUD::resource('preinvoice', 'PreInvoiceCrudController')->middleware('permission:invoice.view');
    CRUD::resource('result', 'ResultCrudController');
});

/* Admin routes */
Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'role:admin'],
    'namespace'  => 'App\Http\Controllers\Admin',
    ], function () { // custom admin routes
    CRUD::resource('period', 'PeriodCrudController');
    CRUD::resource('event', 'EventCrudController');
    CRUD::resource('level', 'LevelCrudController');
    CRUD::resource('room', 'RoomCrudController');
    CRUD::resource('rythm', 'RythmCrudController');
    CRUD::resource('year', 'YearCrudController');
    CRUD::resource('campus', 'CampusCrudController');
    CRUD::resource('user', 'UserCrudController');
    CRUD::resource('evaluationtype', 'EvaluationTypeCrudController');
    CRUD::resource('gradetype', 'GradeTypeCrudController');
    CRUD::resource('skill', 'SkillCrudController');
    CRUD::resource('skilltype', 'SkillTypeCrudController');
    CRUD::resource('skillscale', 'SkillScaleCrudController');
    CRUD::resource('resulttype', 'ResultTypeCrudController');
}); // this should be the absolute last line of this file