<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Leave;
use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Traits\PeriodSelection;
use Illuminate\Support\Facades\Log;
use App\Exceptions\MissingBaseTables;

class HomeController extends Controller
{
    use PeriodSelection;

    public function __construct()
    {
        parent::__construct();

        $this->middleware(backpack_middleware());

    }

    /**
     * redirect the user according to their role
     */
    public function index()
    {
        if(backpack_user()->hasRole(['admin', 'secretary', 'manager']))
        {
            return redirect()->route('admin');
        }
        elseif (backpack_user()->isTeacher()) {
            return redirect()->route('teacherDashboard');
        }
        elseif (backpack_user()->isStudent()) {
            return redirect()->route('studentDashboard');
        }
        else {
            // this should never happen
            Log::warning(backpack_user()->id . ' accessed the generic dashboard (no role identified)');

            return view('welcome');
        }
    }

    public function teacher(Request $request)
    {
        if(!backpack_user()->isTeacher())
        {
            abort(403);
        }

        $period = $this->selectPeriod($request);

        $teacher = Teacher::where('user_id', backpack_user()->id)->first();
        Log::info($teacher->name . ' accessed the student dashboard');

        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'courses' => $teacher->period_courses($period),
            'pending_attendance' => $teacher->events_with_pending_attendance,
            'period' => $period
        ]);
    }


    public function student()
    {
        if(!backpack_user()->isStudent())
        {
            abort(403);
        }

        $student = Student::where('user_id', backpack_user()->id)->first();
        Log::info($student->name . ' accessed the student dashboard');
        
        return view('student.dashboard', [
            'student' => $student,
            'enrollments' => $student->real_enrollments,
        ]);
    }

    public function admin()
    {
        $period = Period::get_default_period();

        if(!backpack_user()->hasRole(['admin', 'secretary', 'manager']))
        {
            abort(403);
        }
        
        Log::info(backpack_user()->firstname . ' ' . backpack_user()->lastname . " accessed the admin dashboard");


        // todo refactor this !!
        $events = Event::orderBy('id', 'desc')->get()->toArray();
        
        $teachers = Teacher::with('user')->get()->toArray();

        $teachers = array_map(function($teacher) {
            return array(
                'id' => $teacher['id'],
                'title' => $teacher['user']['firstname'],
            );
        }, $teachers);

        $events = array_map(function($event) {
            return array(
                'title' => $event['name'],
                'resourceId' => $event['teacher_id'],
                'start' => $event['start'],
                'end' => $event['end'],
            );
        }, $events);

        return view('admin.dashboard', [
            'pending_enrollment_count' => $period->pending_enrollments_count,
            'paid_enrollment_count' => $period->paid_enrollments_count,
            'students_count' => $period->students_count,
            'period' => $period,
            'total_enrollment_count' => $period->total_enrollments_count,
            'pending_attendance' => (new Attendance)->get_pending_attendance(),
            'unassigned_teacher' => (new Event)->unassigned_teacher,
            'upcoming_leaves' => Leave::upcoming_leaves(),
            'resources' => $teachers,
            'events' => $events,
            'call_leads' => Student::where('lead_type_id', 5)->count(),
            'pending_leads' => Student::where('lead_type_id', 4)->count(),
        ]);
    }

}
