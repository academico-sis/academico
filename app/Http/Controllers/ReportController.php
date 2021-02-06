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
        $years = [];

        foreach ($periods as $data_period) {
            $data[$data_period->id]['period'] = $data_period->name;
            $data[$data_period->id]['year_id'] = $data_period->year_id;

            $data[$data_period->id]['enrollments'] = $data_period->internal_enrollments_count;
            $data[$data_period->id]['students'] = $data_period->students_count;
            $data[$data_period->id]['acquisition_rate'] = $data_period->acquisition_rate;
            $data[$data_period->id]['new_students'] = $data_period->new_students_count;
            $data[$data_period->id]['taught_hours'] = $data_period->period_taught_hours_count;
            $data[$data_period->id]['sold_hours'] = $data_period->period_sold_hours_count;
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
            $data[$i]['level'] = $coursegroup[0]->level->reference;
            $data[$i]['enrollment_count'] = $coursegroup->sum('enrollments_count');
            $data[$i]['taught_hours_count'] = $coursegroup->sum('volume');

            $total = 0;
            foreach ($coursegroup as $course) {
                $total += $course->volume * $course->real_enrollments->count();
            }

            $data[$i]['sold_hours_count'] = $total;
        }

        return view('reports.levels', [
            'selected_period' => $period,
            'data' => $data,
        ]);
    }
}
