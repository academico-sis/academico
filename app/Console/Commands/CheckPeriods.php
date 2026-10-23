<?php

namespace App\Console\Commands;

use App\Models\Period;
use App\Services\Settings;
use App\Traits\RunsAtInstitutionTime;
use App\Traits\RunsForEachTenant;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckPeriods extends Command
{
    use RunsAtInstitutionTime;
    use RunsForEachTenant;

    protected $signature = 'academico:check-periods
        {--at= : Only run at this time (HH:MM, on a quarter of an hour) in the institution timezone}
        {--day= : Only run on this day of the month in the institution timezone}
        {--tenant=* : Only run for these tenants (ID or name)}';

    protected $description = 'Stop forcing the current period once it has ended, and the enrollment period once it is the current period';

    public function handle(): int
    {
        if (! $this->isDue() || ! Period::exists()) {
            return Command::SUCCESS;
        }

        if (Carbon::parse(Period::get_default_period()->end) < Carbon::now()) {
            app(Settings::class)->set(['current_period' => null]);
        }

        if (Period::get_enrollments_period() == Period::get_default_period()) {
            app(Settings::class)->set(['default_enrollment_period' => null]);
        }

        return Command::SUCCESS;
    }
}
