<?php

namespace App\Traits;

use App\Models\Period;
use Illuminate\Http\Request;

trait PeriodSelection
{
    public $currentPeriod;

    public function __construct()
    {
        $this->currentPeriod = Period::get_default_period()->id;
    }

    protected function selectPeriod(Request $request)
    {
        $period_id = $request->query('period', null);
        if ($period_id == null) {
            $period = Period::get_default_period();
        } else {
            $period = Period::find($period_id);
        }

        return $period;
    }
}
