<?php

/*
|--------------------------------------------------------------------------
| Backpack\PermissionManager Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the Backpack\PermissionManager package.
|
*/

Route::prefix(config('backpack.base.route_prefix', 'admin'))->middleware('web', 'role:admin')->group(function () {
    Route::crud('permission', 'Backpack\PermissionManager\app\Http\Controllers\PermissionCrudController');
    Route::crud('role', 'Backpack\PermissionManager\app\Http\Controllers\RoleCrudController');
    //Route::crud('user', 'UserCrudController');
});
