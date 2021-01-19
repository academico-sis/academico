<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Traits\PeriodSelection;
use Carbon\Carbon;
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
        $period = $this->selectPeriod($request);

        $report_start_date = $request->report_start_date ?? $period->start;
        $report_end_date = $request->report_end_date ?? $period->end;

        $teachers = Teacher::all();

        Log::info('HR Dahsboard viewed by '. backpack_user()->firstname);

        return view('hr.dashboard', [
            'selected_period' => $period,
            'teachers' => $teachers,
            'start' => Carbon::parse($report_start_date)->format('Y-m-d'),
            'end' => Carbon::parse($report_end_date)->format('Y-m-d'),
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
        ]);
    }
}
