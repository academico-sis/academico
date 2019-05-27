<?php

use App\Models\Event;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\AttendanceType;

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
Route::group(
    ['middleware' => ['auth:api', 'cors']],
    function () {

        Route::get('/attendance', function () {
            return Teacher::where('user_id', request()->user()->id)->firstOrFail()->events_with_pending_attendance;
        });
        
        Route::get('/teacherinfo', function () {
            return Teacher::where('user_id', request()->user()->id)->firstOrFail();
        });

        Route::get('/event/{event}/students', function (Event $event) {
                // get students
                $enrollments = $event->enrollments()->with('student')->get();
            
                // get the attendance record for the event
                $attendance = $event->attendance;
                
                $attendances = [];
                // build a collection : for each student, display attendance

                foreach($enrollments as $e => $enrollment)
                {
                    $attendances[$e]['student'] = $enrollment->student->name;
                    $attendances[$e]['student_id'] = $enrollment->student->id;
                    $attendances[$e]['attendance'] = $attendance->where('student_id', $enrollment->student->id)->first() ?? 'undefined';
                }

                return $attendances;
        });


        Route::post('/attendance', function (Request $request) {

            $student = Student::findOrFail($request->input('body.student_id'));
            $event = Event::findOrFail($request->input('body.event_id'));
            $attendance_type = AttendanceType::findOrFail($request->input('body.attendance_type_id'));

                $attendance = Attendance::firstOrNew([
                    'student_id' => $student->id,
                    'event_id' => $event->id,
                ]);

                $attendance->attendance_type_id = $attendance_type->id;

                $attendance->save();

        });
    });