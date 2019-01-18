<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Event;
use App\Models\Period;
use App\Models\User;
use App\Models\AttendanceType;
use Carbon\Carbon;

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

        // get past events for the course
        $events = $course->events->filter(function ($value, $key) {
            return Carbon::parse($value->start) < Carbon::now();
        })->sortBy('start');


        // if the course has any past events
        if($events->count() > 0) {

        $students = $course->enrollments()->with('student_data')->get();


        foreach($students as $student)
        {
            foreach ($events as $event)
            {
                $attendances[$student->student_data->id]['student'] = $student->student_data->firstname . ' ' . $student->student_data->lastname;
                $attendances[$student->student_data->id][]['attendance'] = $event->attendance->where('user_id', $student->student_data->id)->first();
            }
        }
        
        return view('attendance/course', compact('attendances', 'course', 'events'));
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
            $attendances[$student->student_data->id]['attendance'] = $attendance->where('user_id', $student->student_data->id)->first() ?? '[attendance][attendance_type_id]';
        }
        
        return view('attendance/event', compact('attendances', 'event'));
    }

}
