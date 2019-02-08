<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Period;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Traits\PeriodSelection;

class HRController extends Controller
{

    use PeriodSelection;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:hr.view']);
    }

    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teachers = Teacher::with('remote_events')->with('events')->get();

        $period = $this->selectPeriod($request);

        return view('hr.dashboard', [
            'selected_period' => $period,
            'teachers' => $teachers
        ]);
    }

    public function teacher(Request $request, Teacher $teacher)
    {
        $period = $this->selectPeriod($request);

        return view('teacher.hours', [
            'selected_period' => $period,
            'teacher' => $teacher,
            'events' => $teacher->period_events($period),
            'remote_events' => $teacher->period_remote_events($period),

        ]);
    }

}
