<?php

namespace App\Traits;

use App\Models\Period;
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

    protected function selectPeriod(Request $request)
    {
        $period_id = $request->query('period');

        return $period_id == null ? Period::get_default_period() : Period::find($period_id);
    }
}
