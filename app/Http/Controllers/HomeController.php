<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Leave;
use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Traits\PeriodSelection;
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
        if(backpack_user()->hasRole(['admin', 'secretary']))
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
            return view('welcome');
        }
    }

    public function teacher(Request $request)
    {

        $period = $this->selectPeriod($request);

        $teacher = Teacher::findOrFail(backpack_user()->id);

        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'courses' => $teacher->period_courses($period),
            'pending_attendance' => $teacher->events_with_pending_attendance,
            'period' => $period
        ]);
    }


    public function student()
    {

        $student = Student::find(backpack_user()->id);
        
        return view('student.dashboard', [
            'student' => $student,
            'enrollments' => $student->real_enrollments,
        ]);
    }

    public function admin()
    {
        $period = Period::get_default_period();

        if(!backpack_user()->hasRole(['admin', 'secretary']))
        {
            abort(403);
        }
        
        return view('admin.dashboard', [
            'pending_enrollment_count' => $period->pending_enrollments_count,
            'paid_enrollment_count' => $period->paid_enrollments_count,
            'students_count' => $period->students_count,
            'period' => $period,
            'total_enrollment_count' => $period->total_enrollments_count,
            'pending_attendance' => (new Attendance)->get_pending_attendance(),
            'unassigned_teacher' => (new Event)->unassigned_teacher,
            'upcoming_leaves' => Leave::upcoming_leaves(),
        ]);
    }

}
