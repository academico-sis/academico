<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\LeadType;
use Illuminate\Http\Request;
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
        return view('setup.dashboard', compact('queue', 'failed', 'lead_types', 'orphan_students'));
    }

}
