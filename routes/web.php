<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');
Route::get('courses/create', 'CourseController@create');
Route::post('courses', 'CourseController@store');

Route::get('courses/{period?}', 'CourseController@index');
Route::get('course/{course}', 'CourseController@show');
Route::get('course/{course}/delete', 'CourseController@destroy');
Route::delete('courses', 'CourseController@destroy');

Route::get('students/get', 'StudentController@get');
Route::resource('students', 'StudentController');
