<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Period;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * redirect the user according to their role
     */
    public function index()
    {
        if(backpack_user()->hasRole('admin'))
        {
            return redirect('/admin');
        }
        elseif (backpack_user()->hasRole('teacher')) {
            return redirect('/teacher/dashboard');
        }
        elseif (backpack_user()->hasRole('student')) {
            return redirect('/student/dashboard');
        }
        else {
            // this should never happen
            return view('welcome');
        }
    }

    public function teacher()
    {
        $teacher = backpack_user();
        $period = Period::get_default_period();
        
        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'courses' => $teacher->current_courses,
            'pending_attendance' => (new Attendance)->get_pending_attendance($teacher)
        ]);
    }

    public function admin()
    {
        $period = Period::get_default_period();

        return view('admin.dashboard', [
            'pending_enrollment_count' => $period->pending_enrollments_count,
            'paid_enrollment_count' => $period->paid_enrollments_count,
            'students_count' => $period->students_count,
            'pending_attendance' => (new Attendance)->get_pending_attendance()
        ]);
    }

}
