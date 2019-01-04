<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Event;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $students = $course->enrollments()->with('student_data')->get();
        return view('attendance/course', compact('students', 'course'));
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
