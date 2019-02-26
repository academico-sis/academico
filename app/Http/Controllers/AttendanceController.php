<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Course;
use App\Models\Period;
use App\Models\Student;
use App\Models\Attendance;

use Illuminate\Http\Request;
use App\Models\AttendanceType;
use App\Traits\PeriodSelection;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{

    use PeriodSelection;

    public function __construct()
    {
        $this->middleware('permission:attendance.view');
        $this->middleware('permission:attendance.edit', ['only' => ['store']]);
    }


    /**
     * Monitor attendance for all students
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        Log::info('Attendance dashboard viewed by ' . \backpack_user()->id);
        $period = $this->selectPeriod($request);

        $absences = (new Attendance)->get_absence_count($period);

        $pending_attendance = (new Attendance)->get_pending_attendance();

        return view('attendance.monitor', compact('absences', 'pending_attendance'));
    }

    public function student(Request $request, Student $student)
    {
        $period = $this->selectPeriod($request);

        return view('attendance.student', [
            'student' => $student,
            'selected_period' => $period,
            'absences'=> $student->periodAbsences($period)->get()
        ]);

    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $student = Student::findOrFail($request->input('student_id'));
        $event = Event::findOrFail($request->input('event_id'));
        $attendance_type = AttendanceType::findOrFail($request->input('attendance_type_id'));

        if ($event->teacher_id == \backpack_user()->id || $event->course->teacher_id == \backpack_user()->id || \backpack_user()->hasRole('admin'))
        { 

            $attendance = Attendance::firstOrNew([
                'student_id' => $student->id,
                'event_id' => $event->id,
            ]);
            $attendance->attendance_type_id = $attendance_type->id;
    
            $attendance->save();
    
            Log::info('Attendance recorded by ' . \backpack_user()->id);
            
        } else {
            
            abort(403);
        }
        


    }

    /**
     * Show the attendance records for a course
     */
    public function showCourse(Course $course)
    {
        // get past events for the course
        $events = $course->events->filter(function ($value, $key) {
            return Carbon::parse($value->start) < Carbon::now();
        })->sortBy('start');

        // if the course has any past events
        if($events->count() > 0) {

            $enrollments = $course->enrollments()->with('student')->get();

            foreach($enrollments as $enrollment)
            {
                foreach ($events as $event)
                {
                    $attendances[$enrollment->student->id]['student'] = $enrollment->student->firstname . ' ' . $enrollment->student->lastname;
                    $attendances[$enrollment->student->id][]['attendance'] = $event->attendance->where('student_id', $enrollment->student->id)->first();
                }
            }
            Log::info('Attendance for course viewed by ' . \backpack_user()->id);

            return view('attendance/course', compact('attendances', 'course', 'events'));
        }

        else {
            \Alert::warning(__('The course has no events'))->flash();
            return back();
        }
    }

    public function showEvent(Event $event)
    {
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
        Log::info('Attendance for event viewed by ' . \backpack_user()->id);

        return view('attendance/event', compact('attendances', 'event'));
    }

}
