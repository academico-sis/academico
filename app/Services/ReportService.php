<?php

namespace App\Services;

use App\Models\CachedReport;
use App\Models\Period;
use App\Traits\ReportsErrors;
use Illuminate\Support\Facades\DB;

class ReportService
{
    use ReportsErrors;

    public function buildInternalCoursesReport(Period $startperiod): void
    {
        try {
            Period::orderBy('year_id')->orderBy('order')->orderBy('id')
                ->where('id', '>=', $startperiod->id)
                ->get()
                ->groupBy('year_id')
                ->each(function ($yearData) {
                    /** @var Period $firstPeriod */
                    $firstPeriod = $yearData->first();
                    $year = $firstPeriod->year;
                    $enrollments = 0;
                    $taught_hours = 0;
                    $sold_hours = 0;
                    $takings = 0;
                    $avg_takings = 0;

                    /** @var Period $data_period */
                    foreach ($yearData as $data_period) {
                        $stats = new StatService(external: false, reference: $data_period);

                        $studentsCount = $stats->studentsCount();
                        $enrollmentsCount = $stats->enrollmentsCount();
                        $taughtHoursCount = $stats->taughtHoursCount();
                        $soldHoursCount = $stats->soldHoursCount();

                        DB::table(CachedReport::TABLE_NAME)->insert((new CachedReport(
                            periodName: $data_period->name,
                            yearId: $data_period->year_id,
                            periodId: $data_period->id,
                            students: $studentsCount,
                            enrollments: $enrollmentsCount,
                            acquisitionRate: $data_period->acquisition_rate,
                            newStudents: $data_period->newStudents()->count(),
                            taughtHours: $taughtHoursCount,
                            soldHours: $soldHoursCount,
                            takings: config('academico.include_takings_in_reports') ? $data_period->takings : null,
                            avgTakings: config('academico.include_takings_in_reports') ? $data_period->takings / max(1, $taughtHoursCount) : null,
                            order: $data_period->order,
                        ))->toArray());

                        $enrollments += $enrollmentsCount;
                        $taught_hours += $taughtHoursCount;
                        $sold_hours += $soldHoursCount;

                        if (config('academico.include_takings_in_reports')) {
                            $takings += $data_period->takings;
                            $avg_takings += $data_period->takings / max(1, $taughtHoursCount);
                        }

                        unset($stats);
                        gc_collect_cycles();
                    }

                    $yearStats = new StatService(external: false, reference: $year);

                    $yearData = new CachedReport(
                        periodName: $year->name,
                        yearId: null,
                        periodId: $year->id,
                        students: $yearStats->studentsCount(),
                        enrollments: $enrollments,
                        acquisitionRate: null,
                        newStudents: null,
                        taughtHours: $taught_hours,
                        soldHours: $sold_hours,
                        takings: config('academico.include_takings_in_reports') ? $takings : null,
                        avgTakings: config('academico.include_takings_in_reports') ? $avg_takings / count($yearData) : null,
                    );

                    DB::table(CachedReport::TABLE_NAME)->insert($yearData->toArray());
                });
        } catch (\Throwable $e) {
            $this->reportError($e, 'ReportService::buildInternalCoursesReport', [
                'start_period_id' => $startperiod->id,
            ]);

            throw $e;
        }
    }
}
