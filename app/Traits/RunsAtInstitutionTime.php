<?php

namespace App\Traits;

use App\Models\Tenant;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * For commands that run at a time of day in the institution timezone (settings.courses_timezone).
 *
 * The scheduler starts them every 15 minutes with --at, and --day for monthly tasks. The command only
 * works when the local time falls in that quarter of an hour, once even when clocks go back. Without
 * these options, it works right away. The time is read when the command starts: for several tenants, a
 * slow tenant does not push the next ones into the next quarter of an hour. The command signature declares
 * both options:
 *
 *     {--at= : Only run at this time (HH:MM, on a quarter of an hour) in the institution timezone}
 *     {--day= : Only run on this day of the month in the institution timezone}
 */
trait RunsAtInstitutionTime
{
    private CarbonImmutable $startedAt;

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->startedAt = CarbonImmutable::now();

        return parent::run($input, $output);
    }

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

        $now = ($this->startedAt ?? CarbonImmutable::now())->setTimezone(config('settings.courses_timezone'));

        if ($day !== null && $now->day !== (int) $day) {
            return false;
        }

        if ($at !== null && $now->floorMinutes(15)->format('H:i') !== $at) {
            return false;
        }

        // The tenant is part of the key, in case the cache store ignores the tenant prefix
        $tenant = Tenant::current()?->getKey();

        return Cache::add("scheduled-command:{$tenant}:{$this->getName()}:{$now->toDateString()}:{$at}", true, now()->addDay());
    }
}
