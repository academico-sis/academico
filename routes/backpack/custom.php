<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.




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