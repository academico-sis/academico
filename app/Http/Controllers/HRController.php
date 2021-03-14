<?php

namespace App\Http\Controllers;

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
        $period = $this->selectPeriod($request);

        $teachers = Teacher::with('remote_events')->with('events')->with('courses')->get();

        foreach ($teachers as $teacher) {
            $teacher->remoteVolume = $teacher->courses()->whereNull('parent_course_id')->where('period_id', $period->id)->sum('remote_volume');
            $teacher->volume = $teacher->courses()->whereNull('parent_course_id')->where('period_id', $period->id)->sum('volume');
        }

        Log::info('HR Dahsboard viewed by '.backpack_user()->firstname);

        return view('hr.dashboard', [
            'selected_period' => $period,
            'teachers' => $teachers,
        ]);
    }
}
