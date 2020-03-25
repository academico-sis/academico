<?php

namespace App\Http\Controllers;

use App\Models\LeadType;
use App\Models\Period;
use App\Models\Student;
use App\Traits\PeriodSelection;

class SetupController extends Controller
{
    use PeriodSelection;

    public function __construct()
    {
        parent::__construct();

        $this->middleware(['role:admin']);
    }

    public function index()
    {
        $queue = \Queue::size();
        $failed = \DB::table('failed_jobs')->count();
        $lead_types = LeadType::withCount('students')->get();
        $orphan_students = Student::where('lead_type_id', null)->count();

        $currentPeriod = Period::get_default_period();
        $enrollmentsPeriod = Period::get_enrollments_period();

        return view('setup.dashboard', compact('queue', 'failed', 'lead_types', 'orphan_students', 'currentPeriod', 'enrollmentsPeriod'));
    }
}
