<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\PeriodSelection;

class SetupController extends Controller
{
    use PeriodSelection;

    public function __construct()
    {
        parent::__construct();

        $this->middleware(backpack_middleware());
    }

    public function index()
    {
        $queue = \Queue::size();
        $failed = \DB::table('failed_jobs')->count();
        return view('setup.dashboard', compact('queue', 'failed'));
    }
}
