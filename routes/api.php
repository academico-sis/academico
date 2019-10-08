<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::get('/courses', 'Api\CourseController@getCourses')->middleware('cors');
Route::post('checkemail', '\App\Http\Controllers\Auth\RegisterController@checkEmailUnicity');