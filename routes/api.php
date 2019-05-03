<?php

use App\Models\Event;
use App\Models\Teacher;
use App\Models\Attendance;
use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/attendance', function () {
    return Teacher::where('user_id', request()->user()->id)->firstOrFail()->events_with_pending_attendance;
});
 
Route::middleware('auth:api')->get('/teacherinfo', function () {
    return Teacher::where('user_id', request()->user()->id)->firstOrFail();
});

Route::middleware('auth:api')->get('/attendance/{event}/students', function () {
    return Event::find('event')->enrollments;
});