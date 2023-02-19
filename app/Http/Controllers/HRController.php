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

        $report_start_date = $request->report_start_date ? Carbon::parse($request->report_start_date) : Carbon::parse($period->start);
        $report_end_date = $request->report_end_date ? Carbon::parse($request->report_end_date) : Carbon::parse($period->end);

        $computeTheoreticalValues = false;

        if (! $request->report_start_date && ! $request->report_end_date) {
            // ensure the report end date is not before the end date to avoid inconsistent results.
            $report_end_date = $report_start_date->max($report_end_date);
            $computeTheoreticalValues = true;
        }

        $start = $report_start_date->format('Y-m-d');
        $end = $report_end_date->format('Y-m-d');

        $teacherHours = Teacher::all()->map(function(Teacher $teacher) use ($end, $start, $computeTheoreticalValues, $period, $report_end_date, $report_start_date, $request) {


            return [
                'name' => $teacher->name,
                'remoteVolume' => $computeTheoreticalValues ? number_format($teacher->courses()->realcourses()->where('period_id', $period->id)->sum('remote_volume'), 2) : null,
                'volume' => $computeTheoreticalValues ? number_format($teacher->courses()->realcourses()->where('period_id', $period->id)->sum('volume'), 2) : null,
                'realVolume' => number_format($teacher->plannedHoursInPeriod($start, $end), 2),
                'realRemoteVolume' => number_format($teacher->plannedRemoteHoursInPeriod($start, $end), 2),
            ];
        });


        Log::info('HR Dahsboard viewed by '.backpack_user()->firstname);


        return view('hr.dashboard', [
            'selected_period' => $period,
            'teacherHours' => $teacherHours,
            'start' => $start,
            'end' => $end,
        ]);
    }
}
