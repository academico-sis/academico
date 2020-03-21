<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Period;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function get()
    {
        if (! backpack_user()->hasPermissionTo('enrollments.edit')) {
            abort(403);
        }

        $currentPeriod = Period::get_default_period();
        $enrollmentsPeriod = Period::get_enrollments_period();
        $availablePeriods = Period::all();

        return view('admin.defaultPeriodsSelection', compact('currentPeriod', 'enrollmentsPeriod', 'availablePeriods'));
    }

    public function update(Request $request)
    {
        if (! backpack_user()->hasPermissionTo('courses.edit')) {
            abort(403);
        }

        $request->validate([
            'currentPeriod' => 'required',
            'enrollmentsPeriod' => 'required',
        ]);

        Config::where('name', 'current_period')->first()->update([
            'value' => $request->currentPeriod,
        ]);

        Config::where('name', 'default_enrollment_period')->first()->update([
            'value' => $request->enrollmentsPeriod,
        ]);

        return redirect('/');
    }
}
