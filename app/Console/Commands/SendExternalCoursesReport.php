<?php

namespace App\Console\Commands;

use App\Events\ExternalCoursesReportEvent;
use App\Traits\RunsAtInstitutionTime;
use App\Traits\RunsForEachTenant;
use Illuminate\Console\Command;

class SendExternalCoursesReport extends Command
{
    use RunsAtInstitutionTime;
    use RunsForEachTenant;

    protected $signature = 'academico:external-courses-report
        {--at= : Only run at this time (HH:MM, on a quarter of an hour) in the institution timezone}
        {--day= : Only run on this day of the month in the institution timezone}
        {--tenant=* : Only run for these tenants (ID or name)}';

    protected $description = 'Send the external courses report to the partners whose report day is today, when the report is turned on';

    public function handle(): int
    {
        if (! $this->isDue()) {
            return Command::SUCCESS;
        }

        if (! config('settings.external_courses_report')) {
            $this->info('The external courses report is turned off.');

            return Command::SUCCESS;
        }

        event(new ExternalCoursesReportEvent);

        return Command::SUCCESS;
    }
}
