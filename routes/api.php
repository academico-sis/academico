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

Route::middleware('auth:api')->get('/event/{event}/students', function (Event $event) {
        // get students
        $enrollments = $event->enrollments()->with('student')->get();
    
        // get the attendance record for the event
        $attendance = $event->attendance;
        
        $attendances = [];
        // build a collection : for each student, display attendance

        foreach($enrollments as $enrollment)
        {
            $attendances[$enrollment->student->id]['student'] = $enrollment->student->name;
            $attendances[$enrollment->student->id]['student_id'] = $enrollment->student->id;
            $attendances[$enrollment->student->id]['attendance'] = $attendance->where('student_id', $enrollment->student->id)->first() ?? '[attendance][attendance_type_id]';
        }

        return collect($attendances);
});