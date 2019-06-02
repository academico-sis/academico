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

Route::get('/courses', 'Api\CourseController@getCourses')->middleware('cors');

Route::group(
    [
        'middleware' => ['auth:api', 'cors'],
        'namespace'  => '\App\Http\Controllers\Api',
    ],
    function () {

        Route::get('/attendance', 'ApiAttendanceController@getTeacherAttendance');
        
        Route::get('/teacherinfo', 'ApiAttendanceController@getTeacherInfo');

        Route::get('/event/{event}/students', 'ApiAttendanceController@getEventStudents');

        Route::post('/attendance', 'ApiAttendanceController@post');
    });