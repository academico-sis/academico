<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\CachedReport;
use App\Models\Config;
use App\Models\Course;
use App\Models\Period;
use App\Models\Year;
use App\Services\StatService;
use App\Traits\PeriodSelection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use PeriodSelection;

    public function internal(Request $request)
    {
        $startperiod = $this->getStartperiod($request);

        $data = DB::table(CachedReport::TABLE_NAME)
            ->whereNull('year_id')
            ->orderBy('order')
            ->orderBy('period_name')
            ->get()
            ->map(fn ($y) => CachedReport::from($y))
            ->map(function (CachedReport $yearData) use ($startperiod) {
                return [
                    'period' => $yearData->periodName,
                    'students' => $yearData->students,
                    'enrollments' => $yearData->enrollments,
                    'taught_hours' => $yearData->taughtHours,
                    'sold_hours' => $yearData->soldHours,
                    'takings' => $yearData->takings,
                    'avg_takings' => $yearData->avgTakings,
                    'periods' => DB::table(CachedReport::TABLE_NAME)
                        ->where('period_id', '>=', $startperiod->id)
                        ->where('year_id', '=', $yearData->periodId)
                        ->orderBy('order')
                        ->orderBy('period_name')
                        ->get()
                        ->map(fn ($p) => CachedReport::from($p))
                        ->filter(fn ($p) => $p->enrollments > 0)
                        ->map(function (CachedReport $periodData) {
                            return [
                                'period' => $periodData->periodName,
                                'id' => $periodData->periodId,
                                'students' => $periodData->students,
                                'enrollments' => $periodData->enrollments,
                                'taught_hours' => $periodData->taughtHours,
                                'sold_hours' => $periodData->soldHours,
                                'acquisition_rate' => $periodData->acquisitionRate.'%',
                                'new_students' => $periodData->newStudents,
                                'takings' => $periodData->takings,
                                'avg_takings' => $periodData->avgTakings,
                            ];
                        })
                        ->toArray(),
                ];
            })
        ->where(fn ($d) => count($d['periods']) > 0);

        return view('reports.internal', [
            'data' => $data,
            'selected_period' => $startperiod,
        ]);
    }

    public function genderReport(Request $request)
    {
        $startperiod = $this->getStartperiod($request);
        $data = Period::orderBy('year_id')->orderBy('order')->orderBy('id')
            ->where('id', '>=', $startperiod->id)
            ->get()
            ->groupBy('year_id')
            ->map(function ($yearData) {
                $yearPeriods = [];

                foreach ($yearData as $period) {
                    $periodStats = new StatService(external: false, reference: $period);
                    $studentCountInPeriod = $periodStats->studentsCount();

                    if ($studentCountInPeriod === 0) {
                        continue;
                    }

                    $yearPeriods[$period->id]['period'] = $period->name;
                    $yearPeriods[$period->id]['male'] = $studentCountInPeriod > 0 ? 100 * $periodStats->studentsCount(2) / $studentCountInPeriod : 0;
                    $yearPeriods[$period->id]['female'] = $studentCountInPeriod > 0 ? 100 * $periodStats->studentsCount(1) / $studentCountInPeriod : 0;
                    $yearPeriods[$period->id]['unknown'] = $studentCountInPeriod > 0 ? 100 * $periodStats->studentsCount(0) / $studentCountInPeriod : 0;
                }

                $year = $yearData[0]->year;
                $yearStats = new StatService(external: false, reference: $year);
                $studentCountInYear = $yearStats->studentsCount();

                return [
                    'year' => $year->name,
                    'male' => $studentCountInYear > 0 ? 100 * $yearStats->studentsCount(2) / $studentCountInYear : 0,
                    'female' => $studentCountInYear > 0 ? 100 * $yearStats->studentsCount(1) / $studentCountInYear : 0,
                    'unknown' => $studentCountInYear > 0 ? 100 * $yearStats->studentsCount(0) / $studentCountInYear : 0,
                    'periods' => $yearPeriods,
                ];
            });

        return view('reports.gender', [
            'data' => $data,
            'selected_period' => $startperiod,
        ]);
    }

    /**
     * Show the enrollment numbers per rhythm.
     */
    public function rhythms(Request $request)
    {
        $year = $this->selectYear($request);
        if ($year) {
            $periodGroups = $this->buildRhythmStatsForYear($year);
            $selectedPeriod = ['type' => 'year', 'value' => $year->name];
        } else {
            $period = $this->selectPeriod($request);
            $periodGroups = $this->buildRhythmStatsForPeriod($period);
            $selectedPeriod = ['type' => 'period', 'value' => $period->name];
        }

        $data = [];

        foreach ($periodGroups as $i => $course) {
            $data[$i]['rhythm'] = $course[0]->rhythm->name ?? 'Other';
            $data[$i]['enrollment_count'] = $course->sum('enrollments_count');
        }

        return view('reports.rhythms', [
            'selected_period' => $selectedPeriod,
            'data' => $data,
        ]);
    }

    /** Number of students per course */
    public function courses(Request $request)
    {
        $period = $this->selectPeriod($request);

        $courses = $period->courses()->where('parent_course_id', null)->withCount('enrollments')->orderBy('enrollments_count')->get()->where('enrollments_count', '>', 0);

        $averageStudentCount = $courses->average('enrollments_count');

        return view('reports.courses', [
            'selected_period' => $period,
            'courses' => $courses,
            'averageStudentCount' => round($averageStudentCount, 1),
        ]);
    }

    /** Number of students per level */
    public function levels(Request $request)
    {
        $year = $this->selectYear($request);
        if ($year) {
            $periodGroups = $this->buildLevelStatsForYear($year);
            $selectedPeriod = ['type' => 'year', 'value' => $year->name];
        } else {
            $period = $this->selectPeriod($request);
            $periodGroups = $this->buildLevelStatsForPeriod($period);
            $selectedPeriod = ['type' => 'period', 'value' => $period->name];
        }

        $data = [];

        foreach ($periodGroups as $i => $coursegroup) {
            $data[$i]['level'] = $coursegroup[0]->level->reference ?? __('Other');
            $data[$i]['enrollment_count'] = $coursegroup->sum('enrollments_count');
            $data[$i]['taught_hours_count'] = $coursegroup->sum('total_volume');

            $total = 0;
            foreach ($coursegroup as $course) {
                $total += $course->total_volume * $course->enrollments()->real()->count();
            }

            $data[$i]['sold_hours_count'] = $total;
        }

        return view('reports.levels', [
            'selected_period' => $selectedPeriod,
            'data' => $data,
            'allow_year_selection' => true,
        ]);
    }

    private function getStartperiod(Request $request)
    {
        if (! isset($request->period)) {
            $startperiod = Period::find(Config::where('name', 'first_period')->first()->value);
        } else {
            $startperiod = Period::find($request->period);
        }

        return $startperiod;
    }

    private function buildLevelStatsForPeriod(Period $period): Collection
    {
        return $period->courses()->where('parent_course_id', null)->with('level')->withCount('enrollments')
            ->get()
            ->where('enrollments_count', '>', 0)
            ->groupBy('level.reference');
    }

    private function buildLevelStatsForYear(Year $year): Collection
    {
        $periods = Period::whereYearId($year->id)->pluck('id');

        return Course::whereIn('period_id', $periods)
             ->where('parent_course_id', null)->with('level')->withCount('enrollments')
            ->get()
            ->where('enrollments_count', '>', 0)
            ->groupBy('level.reference');
    }

    public function buildRhythmStatsForPeriod(Period $period): Collection
    {
        return $period->courses()->where('parent_course_id', null)->with('rhythm')->withCount('enrollments')
            ->get()
            ->where('enrollments_count', '>', 0)
            ->groupBy('rhythm_id');
    }

    public function buildRhythmStatsForYear(Year $year): Collection
    {
        $periods = Period::whereYearId($year->id)->pluck('id');

        return Course::whereIn('period_id', $periods)
            ->where('parent_course_id', null)->with('rhythm')->withCount('enrollments')
            ->get()
            ->where('enrollments_count', '>', 0)
            ->groupBy('rhythm_id');
    }
}
