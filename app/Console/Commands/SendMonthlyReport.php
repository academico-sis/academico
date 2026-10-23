<?php

namespace App\Console\Commands;

use App\Events\MonthlyReportEvent;
use App\Traits\RunsAtInstitutionTime;
use Illuminate\Console\Command;

class SendMonthlyReport extends Command
{
    use RunsAtInstitutionTime;

    protected $signature = 'academico:monthly-report
        {--at= : Only run at this time (HH:MM, on a quarter of an hour) in the institution timezone}
        {--day= : Only run on this day of the month in the institution timezone}';

    protected $description = 'Send the monthly report, when it is turned on';

    public function handle(): int
    {
        if (! $this->isDue()) {
            return Command::SUCCESS;
        }

        if (! config('settings.monthly_report')) {
            $this->info('The monthly report is turned off.');

            return Command::SUCCESS;
        }

        event(new MonthlyReportEvent);

        return Command::SUCCESS;
    }
}
