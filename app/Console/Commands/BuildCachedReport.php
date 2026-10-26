<?php

namespace App\Console\Commands;

use App\Models\Period;
use App\Services\ReportService;
use App\Services\Settings;
use App\Traits\ReportsErrors;
use App\Traits\RunsAtInstitutionTime;
use App\Traits\RunsForEachTenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BuildCachedReport extends Command
{
    use ReportsErrors;
    use RunsAtInstitutionTime;
    use RunsForEachTenant;

    protected $signature = 'academico:build-report
        {--at= : Only run at this time (HH:MM, on a quarter of an hour) in the institution timezone}
        {--day= : Only run on this day of the month in the institution timezone}
        {--tenant=* : Only run for these tenants (ID or name)}';

    protected $description = 'Iterate over all periods and update the cached data to display in reports';

    public function __construct(
        private readonly ReportService $reportService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->isDue()) {
            return Command::SUCCESS;
        }

        $this->info('Building cached report data...');

        try {
            DB::table('cached_reports')->truncate();

            $startperiod = Period::find(app(Settings::class)->get('first_period'));

            $this->reportService->buildInternalCoursesReport($startperiod);

            $this->info('Done!');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->reportError($e, 'BuildCachedReport::handle');
            $this->error('Failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
