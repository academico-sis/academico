<?php

namespace App\Console\Commands;

use App\Models\Config;
use App\Models\Period;
use App\Services\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BuildCachedReport extends Command
{
    protected $signature = 'academico:build-report';

    protected $description = 'Iterate over all periods and update the cached data to display in reports';

    public function __construct(
        private readonly ReportService $reportService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Building cached report data...');
        DB::table('cached_reports')->truncate();

        $startperiod = Period::find(Config::where('name', 'first_period')->first()->value);

        $this->reportService->buildInternalCoursesReport($startperiod);

        $this->info('Done!');

        return Command::SUCCESS;
    }
}
