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

Route::prefix(config('backpack.base.route_prefix', 'admin'))->middleware('web', 'role:admin')->group([
            'namespace'  => 'Backpack\\PermissionManager\\app\\Http\\Controllers',], function () {
        Route::crud('permission', 'PermissionCrudController');
        Route::crud('role', 'RoleCrudController');
        //Route::crud('user', 'UserCrudController');
    });
