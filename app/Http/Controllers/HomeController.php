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

    public function index()
    {
        return view('welcome');
    }

    public function teacher()
    {
        $teacher = backpack_user();
        $period = Period::get_default_period();
        
        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'courses' => $teacher->current_courses,
            'pending_attendance' => (new Attendance)->get_pending_attendance($period, $teacher)
        ]);
    }

}
