<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.


Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {
    CRUD::resource('user', '\App\Http\Controllers\Admin\UserCrudController');
});

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

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    CRUD::resource('period', 'PeriodCrudController');
    CRUD::resource('course', 'CourseCrudController');
    CRUD::resource('event', 'EventCrudController');
    CRUD::resource('level', 'LevelCrudController');
    CRUD::resource('room', 'RoomCrudController');
    CRUD::resource('rythm', 'RythmCrudController');
    CRUD::resource('year', 'YearCrudController');
    CRUD::resource('campus', 'CampusCrudController');
    CRUD::resource('user', 'UserCrudController');


}); // this should be the absolute last line of this file