<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * For commands that run at a time of day in the institution timezone (settings.courses_timezone).
 *
 * The scheduler starts them every 15 minutes with --at, and --day for monthly tasks. The command only
 * works when the local time falls in that quarter of an hour, once even when clocks go back. Without
 * these options, it works right away. The command signature declares both options:
 *
 *     {--at= : Only run at this time (HH:MM, on a quarter of an hour) in the institution timezone}
 *     {--day= : Only run on this day of the month in the institution timezone}
 */
trait RunsAtInstitutionTime
{
    protected function isDue(): bool
    {
        $at = $this->option('at');
        $day = $this->option('day');

        if ($at === null && $day === null) {
            return true;
        }

        if ($at !== null && ! preg_match('/^([01]\d|2[0-3]):(00|15|30|45)$/', $at)) {
            throw new InvalidArgumentException("--at must be a quarter of an hour, such as 08:15, not {$at}.");
        }

        $now = Carbon::now(config('settings.courses_timezone'));

        if ($day !== null && $now->day !== (int) $day) {
            return false;
        }

        if ($at !== null && $now->copy()->floorMinutes(15)->format('H:i') !== $at) {
            return false;
        }

        return Cache::add("scheduled-command:{$this->getName()}:{$now->toDateString()}:{$at}", true, now()->addDay());
    }
}
