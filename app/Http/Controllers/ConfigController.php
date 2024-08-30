<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateConfigRequest;
use App\Models\Config;
use App\Models\Period;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConfigController extends Controller
{
    public function get(): View
    {
        if (! backpack_user()->hasPermissionTo('courses.edit')) {
            abort(403);
        }

        $currentPeriod = Period::get_default_period();
        $enrollmentsPeriod = Period::get_enrollments_period();
        $availablePeriods = Period::active()->get();

        return view('admin.defaultPeriodsSelection', compact('currentPeriod', 'enrollmentsPeriod', 'availablePeriods'));
    }

    public function update(UpdateConfigRequest $request): RedirectResponse
    {
        if (! backpack_user()->hasPermissionTo('courses.edit')) {
            abort(403);
        }

        Config::where('name', 'current_period')->first()->update([
            'value' => $request->currentPeriod,
        ]);

        Config::where('name', 'default_enrollment_period')->first()->update([
            'value' => $request->enrollmentsPeriod,
        ]);

        return redirect()->to('/');
    }
}
