<?php

namespace App\Http\Api\Controllers;


use App\Models\Event;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\AttendanceType;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttendanceController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getTeacherAttendance()
    {
        return Teacher::where('user_id', request()->user()->id)->firstOrFail()->events_with_pending_attendance;
    }

    public function getTeacherInfo()
    {
        return Teacher::where('user_id', request()->user()->id)->firstOrFail();
    }

    public function getEventStudents(Event $event) {
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
    }

    public function post(Request $request) {

        $student = Student::findOrFail($request->input('body.student_id'));
        $event = Event::findOrFail($request->input('body.event_id'));
        $attendance_type = AttendanceType::findOrFail($request->input('body.attendance_type_id'));

            $attendance = Attendance::firstOrNew([
                'student_id' => $student->id,
                'event_id' => $event->id,
            ]);

            $attendance->attendance_type_id = $attendance_type->id;

            $attendance->save();

    }


}
