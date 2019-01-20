<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Period;
use Illuminate\Http\Request;

class HRController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teachers = User::teacher()->all();

        $period_id = $request->query('period', null);
        if ($period_id == null) { $period = Period::get_default_period(); }
        else { $period = Period::find($period_id); }

        return view('hr.dashboard', [
            'period' => $period,
            'teachers' => $teachers,
        ]);
    }

}
