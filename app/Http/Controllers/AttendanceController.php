<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Event;
use App\Models\Period;

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
        /* todo deduplicate */
        $period_id = $request->query('period', null);
        if ($period_id == null) { $period = Period::get_default_period(); }
        else { $period = Period::find($period_id); }

        $absences = (new Attendance)->get_absence_count($period);
        //dd($absences);
        $pending_attendance = (new Attendance)->get_pending_attendance($period);
        //return $absences;
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
        return Attendance::where('user_id', $request->query('student'))
            ->where('event_id', $request->query('event'))
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function showCourse(Course $course)
    {
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
            
            
            // look if there is a match in attendance record for this user id

        }
        

        //dd($attendances);
        return view('attendance/course', compact('attendances', 'course'));
    }
    else {
        \Alert::warning('The cours has no events')->flash();
        return back();
    }
    }

    public function showEvent(Event $event)
    {
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
            // look if there is a match in attendance record for this user id

        }
        //return $attendances;
        
        return view('attendance/event', compact('attendances'));
        
    }

    public function showStudent(User $user)
    {
        return view('attendance/student', compact('students', 'course'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
