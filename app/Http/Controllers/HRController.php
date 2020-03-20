<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Period;
use App\Models\Teacher;
use App\Traits\PeriodSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class HRController extends Controller
{
    use PeriodSelection;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:hr.view', ['except' => 'teacher']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teachers = Teacher::with('remote_events')->with('events')->get();

        $period = $this->selectPeriod($request);

        Log::info('HR Dahsboard viewed by '.backpack_user()->firstname);

        return view('hr.dashboard', [
            'selected_period' => $period,
            'teachers' => $teachers,
        ]);
    }

    public function teacher(Request $request, Teacher $teacher)
    {
        // If the user is not allowed to perform this action
        if (Gate::forUser(backpack_user())->denies('view-teacher-hours', $teacher)) {
            abort(403);
        }

        $period = $this->selectPeriod($request);

        return view('teacher.hours', [
            'selected_period' => $period,
            'teacher' => $teacher,
            'events' => $teacher->period_events($period),
            'remote_events' => $teacher->period_remote_events($period),

        ]);
    }
}
