<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Period;
use App\Models\Year;
use App\Traits\PeriodSelection;
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

    public function external(Request $request)
    {
        $period = Period::get_default_period();

        $data = [];
        $year_data = [];

        if (! isset($request->period)) {
            $startperiod = Period::find(Config::where('name', 'first_period')->first()->value);
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
        }

        Log::info('Reports viewed by '.backpack_user()->firstname);

        $current_year = \App\Models\Year::find($current_year_id);

        return view('reports.external', [
            'selected_period' => $startperiod,
            'data' => $data,
            'year_data' => $year_data,
            'current_year' => $current_year,
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

        $periods = Period::where('id', '>=', $startperiod->id)->get();

        $data = [];

        $current_year_id = $startperiod->year_id;

        foreach ($periods as $i => $data_period) {
            $data[$data_period->id]['period'] = $data_period->name;
            $data[$data_period->id]['year_id'] = $data_period->year_id;

            $data[$data_period->id]['enrollments'] = $data_period->internal_enrollments_count;
            $data[$data_period->id]['students'] = $data_period->students_count;
            $data[$data_period->id]['acquisition_rate'] = $data_period->acquisition_rate;
            $data[$data_period->id]['new_students'] = $data_period->new_students_count;
            $data[$data_period->id]['taught_hours'] = $data_period->period_taught_hours_count;
            $data[$data_period->id]['sold_hours'] = $data_period->period_sold_hours_count;
        }

        Log::info('Reports viewed by '.backpack_user()->firstname);

        $current_year = \App\Models\Year::find($current_year_id);

        return view('reports.internal', [
            'selected_period' => $period,
            'pending_enrollment_count' => $period->pending_enrollments_count,
            'paid_enrollment_count' => $period->paid_enrollments_count,
            'total_enrollment_count' => $period->internal_enrollments_count,
            'students_count' => $period->students_count,
            'data' => $data,
            'selected_period' => $startperiod,
            'current_year' => $current_year,
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

        foreach ($count as $i => $course) {
            $data[$i]['rhythm'] = $course[0]->rhythm->name;
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

        return view('reports.courses', [
            'selected_period' => $period,
            'courses' => $courses,
        ]);
    }
}
