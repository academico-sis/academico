<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.
    
     
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
        CRUD::resource('student', 'Admin\StudentCrudController');
        CRUD::resource('course', 'Admin\CourseCrudController');
        CRUD::resource('comment', 'Admin\CommentCrudController');
        CRUD::resource('courseskill', 'Admin\CourseSkillCrudController');
        
        Route::post('edit-account-info', 'Auth\MyAccountController@postAccountInfoForm');
        Route::post('edit-student-info', 'Auth\MyAccountController@postStudentInfoForm');
        Route::post('edit-profession', 'Auth\MyAccountController@postAccountProfessionForm');
        Route::post('edit-phone', 'Auth\MyAccountController@postPhoneForm');
        Route::post('edit-photo', 'Auth\MyAccountController@postPhotoForm');
        Route::post('edit-contacts', 'Auth\MyAccountController@postContactsForm');
        }
);


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
    }
);


// enrollments and invoicing

Route::group([
    'prefix'     => config('backpack.base.route_prefix'),
    'middleware' => ['web', 'permission:enrollments.view', 'language'],
    'namespace'  => 'App\Http\Controllers\Admin',
    ], function () {
        CRUD::resource('enrollment', 'EnrollmentCrudController');
        CRUD::resource('availablecourse', 'AvailableCourseCrudController');
    }
);


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