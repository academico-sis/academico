<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Event;
use App\Models\Period;
use App\Models\User;
use App\Models\AttendanceType;


use Illuminate\Http\Request;

class AttendanceController extends Controller
{

    /**
     * Monitor attendance for all students
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->middleware(['permission:attendance.view']);

        /* todo deduplicate */
        $period_id = $request->query('period', null);
        if ($period_id == null) { $period = Period::get_default_period(); }
        else { $period = Period::find($period_id); }

        $absences = (new Attendance)->get_absence_count($period);

        $pending_attendance = (new Attendance)->get_pending_attendance($period);

        return view('attendance.monitor', compact('absences', 'pending_attendance'));
    }


    /**
     * get attendance for a specific event and user
     * 
     * todo refactor to reduce number of queries
     *
     * @param Request $request
     */
    public function get(Request $request)
    {
        $this->middleware(['permission:attendance.view']);

        return Attendance::where('user_id', $request->query('student'))
            ->where('event_id', $request->query('event'))
            ->get();
    }



    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $this->middleware(['permission:attendance.edit']);
        // todo do not give the permission to teachers, and allow this method to users who own the event

        $student = User::findOrFail($request->input('student'));
        $event = Event::findOrFail($request->input('event'));
        $attendance_type = AttendanceType::findOrFail($request->input('attendance'));

        $attendance = Attendance::firstOrNew([
            'user_id' => $student->id,
            'event_id' => $event->id,
        ]);
        $attendance->attendance_type_id = $attendance_type->id;

        $attendance->save();

    }

    /**
     * Show the attendance records for a course
     */
    public function showCourse(Course $course)
    {
        $this->middleware(['permission:attendance.view']);

        // if the course has any events
        if($course->events->count() > 0) {

        $students = $course->enrollments()->with('student_data')->get();

        foreach($students as $student)
        {
            foreach ($course->events as $event)
            {
                $attendances[$student->student_data->id]['student'] = $student->student_data->firstname;
                $attendances[$student->student_data->id][$event->id]['attendance'] = $event->attendance->where('user_id', $student->student_data->id)->first();
            }
        }
        
        return view('attendance/course', compact('attendances', 'course'));
        }

        else {
            \Alert::warning('The cours has no events')->flash();
            return back();
        }
    }

    public function showEvent(Event $event)
    {
        $this->middleware(['permission:attendance.view']);

        // get students
        $students = $event->students;

        // get the attendance record for the event
        $attendance = $event->attendance;
        
        $attendances = [];
        // build a collection : for each student, display attendance if match, otherwise NULL

        foreach($students as $student)
        {
            $attendances[$student->student_data->id]['student'] = $student->student_data;
            $attendances[$student->student_data->id]['attendance'] = $attendance->where('user_id', $student->student_data->id)->first();
        }
        
        return view('attendance/event', compact('attendances', 'event'));
    }

}
