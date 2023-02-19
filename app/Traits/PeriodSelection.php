<?php

namespace App\Traits;

use App\Models\Period;
use App\Models\Year;
use Illuminate\Http\Request;

trait PeriodSelection
{
    public $currentPeriod;

    public function __construct()
    {
        if (Period::count() > 0) {
            $this->currentPeriod = Period::get_default_period()->id;
        }
    }

    protected function selectYear(Request $request): ?Year
    {
        $year_id = $request->query('year');

        return $year_id ? Year::find($year_id) : null;
    }

    protected function selectPeriod(Request $request): Period
    {
        $period_id = $request->query('period');
        if ($period_id == null) {
            $period = Period::get_default_period();
        } else {
            $period = Period::find($period_id);
        }

        return $period;
    }
}
