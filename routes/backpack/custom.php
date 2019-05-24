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

        CRUD::resource('result', 'Admin\ResultCrudController');

        CRUD::resource('preinvoice', 'Admin\PreInvoiceCrudController');
        
        Route::post('edit-account-info', 'Auth\MyAccountController@postAccountInfoForm');
        Route::post('edit-student-info', 'Auth\MyAccountController@postStudentInfoForm');
        Route::post('edit-profession', 'Auth\MyAccountController@postAccountProfessionForm');
        Route::post('edit-phone', 'Auth\MyAccountController@postPhoneForm');
        Route::post('edit-photo', 'Auth\MyAccountController@postPhotoForm');
        Route::post('edit-contacts', 'Auth\MyAccountController@postContactsForm');
        });


Route::group(
    [
        'namespace'  => '\App\Http\Controllers',
        'middleware' => ['web', 'language', 'forceupdate'],
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
        });



// when finer control is needed, move routes here and protect them with policies instead of permissions
Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'language'],
    'namespace'  => 'App\Http\Controllers\Admin',
    ], function () {
        CRUD::resource('student', 'StudentCrudController');
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