<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Course;
use App\Models\Partner;
use App\Models\Period;
use App\Models\Year;
use App\Traits\PeriodSelection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    use PeriodSelection;

    public function index()
    {
        $currentPeriod = Period::get_default_period();
        $enrollmentsPeriod = Period::get_enrollments_period();

        return view('reports.index', [
            'currentPeriod' => $currentPeriod,
            'enrollmentsPeriod' => $enrollmentsPeriod,
            'pending_enrollment_count' => $currentPeriod->pending_enrollments_count,
            'paid_enrollment_count' => $currentPeriod->paid_enrollments_count,
            'total_enrollment_count' => $currentPeriod->internal_enrollments_count,
            'students_count' => $currentPeriod->students_count,
        ]);
    }

    public function external2(Request $request)
    {
        $data = [];
        $report_start_date = $request->report_start_date ?? Carbon::parse('2019-01-01');
        $report_end_date = $request->report_end_date ?? Carbon::now();

        $courses = Course::external()->where('end_date', '>', $report_start_date)->where('start_date', '<', $report_end_date)->get();

        $data['courses'] = $courses->count();
        $data['enrollments'] = $courses->sum('head_count');
        $data['students'] = $courses->sum('new_students');
        $data['taught_hours'] = $courses->where('parent_course_id', null)->sum('total_volume');
        $total = 0;
        foreach ($courses->where('parent_course_id', null) as $course) {
            $total += $course->total_volume * $course->head_count;
        }
        $data['sold_hours'] = $total;

        return view('reports.external2', [
            'start' => Carbon::parse($report_start_date)->format('Y-m-d'),
            'end' => Carbon::parse($report_end_date)->format('Y-m-d'),
            'data' => $data,
            'courses' => $courses,
        ]);
    }

    public function external(Request $request)
    {
        $data = [];
        $year_data = [];
        $years = []; // New array

        if (! isset($request->period)) {
            $startperiod = Period::find(Config::where('name', 'first_external_period')->first()->value ?? Period::first()->id);
        } else {
            $startperiod = Period::find($request->period);
        }

        $periods = Period::where('id', '>=', $startperiod->id)->get();

        $current_year_id = $startperiod->year_id;
        $year_data[$current_year_id] = [];
        $year_data[$current_year_id]['year_name'] = Year::find($current_year_id)->name;
        $year_data[$current_year_id]['students'] = 0;
        $year_data[$current_year_id]['enrollments'] = 0;
        $year_data[$current_year_id]['taught_hours'] = 0;
        $year_data[$current_year_id]['sold_hours'] = 0;

        foreach ($periods as $i => $data_period) {
            $data[$data_period->id]['period'] = $data_period->name;
            $data[$data_period->id]['year_id'] = $data_period->year_id;
            $data[$data_period->id]['courses'] = $data_period->external_courses_count;
            $data[$data_period->id]['partnerships'] = $data_period->partnerships_count;
            $data[$data_period->id]['enrollments'] = $data_period->external_enrollments_count;
            $data[$data_period->id]['students'] = $data_period->external_students_count;
            $data[$data_period->id]['taught_hours'] = $data_period->external_taught_hours_count;
            $data[$data_period->id]['sold_hours'] = $data_period->external_sold_hours_count;

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

            $year_data[$current_year_id]['students'] += $data_period->external_students_count;
            $year_data[$current_year_id]['enrollments'] += $data_period->external_enrollments_count;
            $year_data[$current_year_id]['taught_hours'] += $data_period->external_taught_hours_count;
            $year_data[$current_year_id]['sold_hours'] += $data_period->external_sold_hours_count;

            $year = Year::find($data_period->year_id)->append('partnerships');
            $years[$data_period->year_id]['year'] = $year->name; // New array using the Model
            $years[$data_period->year_id]['partnerships'] = $year->partnerships;
        }

        Log::info('Reports viewed by '.backpack_user()->firstname);

        return view('reports.external', [
            'selected_period' => $startperiod,
            'data' => $data,
            'year_data' => $year_data, // Existing array
            'years' => $years,
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
        $report_start_date = $request->report_start_date ?? Carbon::parse('2019-01-01');
        $report_end_date = $request->report_end_date ?? Carbon::now();

        $courses = Course::external()->where('partner_id', $partner->id)->where('end_date', '>', $report_start_date)->where('start_date', '<', $report_end_date)->get();

        $data['courses'] = $courses->count();
        $data['enrollments'] = $courses->sum('head_count');
        $data['students'] = $courses->sum('new_students');
        $data['taught_hours'] = $courses->where('parent_course_id', null)->sum('total_volume');
        $total = 0;
        foreach ($courses->where('parent_course_id', null) as $course) {
            $total += $course->total_volume * $course->head_count;
        }
        $data['sold_hours'] = $total;

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
        $period = Period::get_default_period();

        if (! isset($request->period)) {
            $startperiod = Period::find(Config::where('name', 'first_period')->first()->value);
        } else {
            $startperiod = Period::find($request->period);
        }

        $periods = Period::orderBy('year_id')->orderBy('order')->orderBy('id')->where('id', '>=', $startperiod->id)->get();

        $data = [];
        $years = [];

        foreach ($periods as $data_period) {
            $data[$data_period->id]['period'] = $data_period->name;
            $data[$data_period->id]['year_id'] = $data_period->year_id;

            $data[$data_period->id]['enrollments'] = $data_period->internal_enrollments_count;
            $data[$data_period->id]['students'] = $data_period->students_count;
            $data[$data_period->id]['acquisition_rate'] = $data_period->acquisition_rate;
            $data[$data_period->id]['new_students'] = $data_period->newStudents()->count();
            $data[$data_period->id]['taught_hours'] = $data_period->period_taught_hours_count;
            $data[$data_period->id]['sold_hours'] = $data_period->period_sold_hours_count;
            $data[$data_period->id]['takings'] = $data_period->takings;
            $data[$data_period->id]['avg_takings'] = $data_period->takings / max(1, $data_period->period_taught_hours_count);
            $years[$data_period->year_id] = Year::find($data_period->year_id); // New array using the Model
        }

        Log::info('Reports viewed by '.backpack_user()->firstname);

        return view('reports.internal', [
            'pending_enrollment_count' => $period->pending_enrollments_count,
            'paid_enrollment_count' => $period->paid_enrollments_count,
            'total_enrollment_count' => $period->internal_enrollments_count,
            'students_count' => $period->students_count,
            'data' => $data,
            'selected_period' => $startperiod,
            'years' => $years, // New array
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
                $total += $course->total_volume * $course->real_enrollments->count();
            }

            $data[$i]['sold_hours_count'] = $total;
        }

        return view('reports.levels', [
            'selected_period' => $period,
            'data' => $data,
        ]);
    }
}
