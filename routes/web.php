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

// custom user CRUDS
Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {
    CRUD::resource('user', '\App\Http\Controllers\Admin\UserCrudController');
});

/* 
Route::group(['middleware' => 'web', 'prefix' => config('backpack.base.route_prefix')], function () {
    Route::auth();
    Route::get('logout', 'Auth\LoginController@logout');
}); */

Route::get('/', 'HomeController@index');
Route::get('courses/create', 'CourseController@create');
Route::post('courses', 'CourseController@store');


Route::get('courses/{period?}', 'CourseController@index');
Route::get('course/{course}', 'CourseController@show');

/* Course Teacher update */
Route::get('course/{course}/teacher', 'CourseTeacherController@edit');
Route::patch('course/{course}/teacher', 'CourseTeacherController@update');

/* Course Room update */
Route::get('course/{course}/room', 'CourseRoomController@edit');
Route::patch('course/{course}/room', 'CourseRoomController@update');

/* Course Evaluation Type update */
Route::get('course/{course}/evaluation', 'CourseEvaluationController@edit');
Route::patch('course/{course}/evaluation', 'CourseEvaluationController@update');

/* Course Times update */
Route::get('course/{course}/time/get', 'CourseTimeController@show');
Route::get('course/{course}/time', 'CourseTimeController@edit');
Route::post('course/{course}/time', 'CourseTimeController@store');
Route::delete('coursetime/{id}', 'CourseTimeController@destroy');


Route::get('course/{course}/delete', 'CourseController@destroy');

Route::delete('courses', 'CourseController@destroy');

Route::get('students/get', 'StudentController@get');
Route::resource('students', 'StudentController');

Route::get('students/{student}/enroll/{period?}', 'EnrollmentController@create');
Route::post('enrollments', 'EnrollmentController@store');
Route::resource('enrollments', 'EnrollmentController');

Route::get('enrollments/{enrollment}/bill', 'EnrollmentController@bill');

// Cart / carts routes
Route::resource('carts', 'CartController');
Route::post('carts/{student}/checkout', 'PreInvoiceController@store');

// add an enrollment to the cart for checkout
Route::post('products', 'ProductController@store');

// remove something from the cart
Route::delete('products', 'ProductController@destroy');


Route::resource('invoices', 'PreInvoiceController');


Route::get('/home', 'HomeController@index')->name('home');
