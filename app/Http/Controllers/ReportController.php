<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Partner;
use App\Models\Period;
use App\Models\Year;
use App\Services\DateRange;
use App\Services\StatService;
use App\Traits\PeriodSelection;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use PeriodSelection;

    public function index()
    {
        $currentPeriod = Period::get_default_period();
        $enrollmentsPeriod = Period::get_enrollments_period();

        $stats = new StatService(external: false, reference: $currentPeriod, partner: null);

        return view('reports.index', [
            'currentPeriod' => $currentPeriod,
            'enrollmentsPeriod' => $enrollmentsPeriod,
            'pending_enrollment_count' => $stats->pendingEnrollmentsCount(),
            'paid_enrollment_count' => $stats->paidEnrollmentsCount(),
            'total_enrollment_count' => $stats->enrollmentsCount(),
            'students_count' => $stats->studentsCount(),
        ]);
    }

    public function external2(Request $request)
    {
        $data = [];
        $report_start_date = Carbon::parse($request->report_start_date) ?? Carbon::parse('2019-01-01');
        $report_end_date = Carbon::parse($request->report_end_date) ?? Carbon::now();

        $stats = new StatService(external: true, reference: new DateRange($report_start_date, $report_end_date), partner: null);

        $data['courses'] = $stats->coursesCount();
        $data['enrollments'] = $stats->enrollmentsCount();
        $data['students'] = $stats->studentsCount();
        $data['taught_hours'] = $stats->taughtHoursCount();
        $data['sold_hours'] = $stats->soldHoursCount();

        return view('reports.external2', [
            'start' => Carbon::parse($report_start_date)->format('Y-m-d'),
            'end' => Carbon::parse($report_end_date)->format('Y-m-d'),
            'data' => $data,
            'courses' => $stats->coursesQuery->get(),
        ]);
    }

    public function external(Request $request)
    {
        $data = [];
        $year_data = [];
        $years = [];

        if (! isset($request->period)) {
            $startperiod = Period::find(Config::where('name', 'first_external_period')->first()->value ?? Period::first()->id);
        } else {
            $startperiod = Period::find($request->period);
        }

        $selectedPartner = Partner::find($request->partner);

        $periods = Period::where('id', '>=', $startperiod->id)->get();

        $current_year_id = $startperiod->year_id;
        $year_data[$current_year_id] = [];
        $year_data[$current_year_id]['year_name'] = Year::find($current_year_id)->name;
        $year_data[$current_year_id]['students'] = 0;
        $year_data[$current_year_id]['enrollments'] = 0;
        $year_data[$current_year_id]['taught_hours'] = 0;
        $year_data[$current_year_id]['sold_hours'] = 0;

        foreach ($periods as $data_period) {
            $periodStats = new StatService(external: true, reference: $data_period, partner: $selectedPartner);

            $data[$data_period->id]['period'] = $data_period->name;
            $data[$data_period->id]['year_id'] = $data_period->year_id;
            $data[$data_period->id]['courses'] = $periodStats->coursesCount();
            $data[$data_period->id]['partnerships'] = $periodStats->partnershipsCount();
            $data[$data_period->id]['enrollments'] = $periodStats->enrollmentsCount();
            $data[$data_period->id]['students'] = $periodStats->studentsCount();
            $data[$data_period->id]['taught_hours'] = $periodStats->taughtHoursCount();
            $data[$data_period->id]['sold_hours'] = $periodStats->soldHoursCount();

            // if we are starting a new year, push the year data to the array
            if ($current_year_id != $data_period->year_id) {
                $current_year_id = $data_period->year_id;

                $year_data[$current_year_id] = [];
                $year_data[$current_year_id]['year_name'] = Year::find($current_year_id)->name;
                $year_data[$current_year_id]['students'] = 0;
                $year_data[$current_year_id]['enrollments'] = 0;
                $year_data[$current_year_id]['taught_hours'] = 0;
                $year_data[$current_year_id]['sold_hours'] = 0;
            }

            $year_data[$current_year_id]['students'] += $data[$data_period->id]['students'];
            $year_data[$current_year_id]['enrollments'] += $data[$data_period->id]['enrollments'];
            $year_data[$current_year_id]['taught_hours'] += $data[$data_period->id]['taught_hours'];
            $year_data[$current_year_id]['sold_hours'] += $data[$data_period->id]['sold_hours'];

            $year = Year::find($data_period->year_id)->append('partnerships');

            $yearStats = new StatService(external: true, reference: $year, partner: $selectedPartner);
            $years[$data_period->year_id]['year'] = $year->name;
            $years[$data_period->year_id]['partnerships'] = $yearStats->partnershipsCount();
        }

        return view('reports.external', [
            'selected_period' => $startperiod,
            'selected_partner' => $selectedPartner,
            'data' => $data,
            'year_data' => $year_data,
            'years' => $years,
            'partners' => Partner::all(),
        ]);
    }

    public function external3()
    {
        return view('reports.external3', [
            'partners' => Partner::all(),
        ]);
    }

    public function partner(Partner $partner, Request $request)
    {
        $data = [];
        $report_start_date = Carbon::parse($request->report_start_date) ?? Carbon::parse('2019-01-01');
        $report_end_date = Carbon::parse($request->report_end_date) ?? Carbon::now();

        $stats = new StatService(external: true, reference: new DateRange($report_start_date, $report_end_date), partner: $partner);

        $data['courses'] = $stats->coursesCount();
        $data['enrollments'] = $stats->enrollmentsCount();
        $data['students'] = $stats->studentsCount();
        $data['taught_hours'] = $stats->taughtHoursCount();
        $data['sold_hours'] = $stats->soldHoursCount();

        return view('reports.partner', [
            'partner' => $partner,
            'start' => Carbon::parse($report_start_date)->format('Y-m-d'),
            'end' => Carbon::parse($report_end_date)->format('Y-m-d'),
            'data' => $data,
        ]);
    }

    /**
     * The reports dashboard
     * Displays last insights on enrollments; along with comparison to previous periods.
     *
     * Todo - optimize this method: is there another way than using an array? How to reduce the number of queries?
     * Todo - Limit to the three last years to keep the figures readable
     */
    public function internal(Request $request)
    {
        $startperiod = $this->getStartperiod($request);

        $chartData = Period::orderBy('year_id')->orderBy('order')->orderBy('id')
            ->where('id', '>=', $startperiod->id)
            ->get()
            ->groupBy('year_id')
            ->map(function ($yearData) {
                $data = [];

                foreach ($yearData as $data_period) {
                    $stats = new StatService(external: false, reference: $data_period);

                    $data[$data_period->id]['period'] = $data_period->name;
                    $data[$data_period->id]['year_id'] = $data_period->year_id;

                    $data[$data_period->id]['enrollments'] = $stats->enrollmentsCount();
                    $data[$data_period->id]['students'] = $stats->studentsCount();
                    $data[$data_period->id]['acquisition_rate'] = $data_period->acquisition_rate;
                    $data[$data_period->id]['new_students'] = $data_period->newStudents()->count();
                    $data[$data_period->id]['taught_hours'] = $stats->taughtHoursCount();
                    $data[$data_period->id]['sold_hours'] = $stats->soldHoursCount();

                    if (config('academico.include_takings_in_reports')) {
                        $data[$data_period->id]['takings'] = $data_period->takings;
                        $data[$data_period->id]['avg_takings'] = $data_period->takings / max(1, $stats->taughtHoursCount());
                    }
                }

                $year = $data_period->year;
                $yearStats = new StatService(external: false, reference: $year);

                $yearOutput = [
                    'year' => $year,
                    'students' => $yearStats->studentsCount(),
                    'enrollments' => collect($data[$data_period->id])->sum('enrollments'),
                    'taught_hours' => collect($data[$data_period->id])->sum('taught_hours'),
                    'sold_hours' => collect($data[$data_period->id])->sum('sold_hours'),
                    'periods' => $data,
                ];

                if (config('academico.include_takings_in_reports')) {
                    $yearOutput['takings'] = collect($data[$data_period->id])->sum('takings');
                    $yearOutput['avg_takings'] = $yearOutput['takings'] / max(1, $stats->taughtHoursCount());
                }

                return $yearOutput;
             });

        return view('reports.internal', [
            'data' => $chartData,
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
        $period = $this->selectPeriod($request);

        $count = $period->courses()->where('parent_course_id', null)->with('rhythm')->withCount('enrollments')
            ->get()
            ->where('enrollments_count', '>', 0)
            ->groupBy('rhythm_id');

        $data = [];

        foreach ($count as $i => $course) {
            $data[$i]['rhythm'] = $course[0]->rhythm->name ?? 'Other';
            $data[$i]['enrollment_count'] = $course->sum('enrollments_count');
        }

        return view('reports.rhythms', [
            'selected_period' => $period,
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
        $period = $this->selectPeriod($request);

        $count = $period->courses()->where('parent_course_id', null)->with('level')->withCount('enrollments')
            ->get()
            ->where('enrollments_count', '>', 0)
            ->groupBy('level.reference');

        $data = [];

        foreach ($count as $i => $coursegroup) {
            $data[$i]['level'] = $coursegroup[0]->level->reference ?? 'Other';
            $data[$i]['enrollment_count'] = $coursegroup->sum('enrollments_count');
            $data[$i]['taught_hours_count'] = $coursegroup->sum('total_volume');

            $total = 0;
            foreach ($coursegroup as $course) {
                $total += $course->total_volume * $course->enrollments()->real()->count();
            }

            $data[$i]['sold_hours_count'] = $total;
        }

        return view('reports.levels', [
            'selected_period' => $period,
            'data' => $data,
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
}
