<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\AttendanceType;
use App\Models\Course;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use App\Traits\PeriodSelection;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    use PeriodSelection;

    public function forCourse(Request $request)
    {
        $period = $this->selectPeriod($request);

        if ($request->has('courseId')) {
            $course = Course::findOrFail($request->get('courseId'));
        } else {
            $course = Course::wherePeriodId($period->id)->has('attendance')->has('events')->first();
        }

        $otherCourses = $period->courses()->has('attendance')->has('events');
        if ($course) {
            $otherCourses = $otherCourses->where('id', '!=', $course->id);
        }

        $datasets = [];

        if (! $course) {
            $labels = [];
        } else {
            $labels = $course ? $course->events->keys()->map(fn ($k) => __('Event').' '.$k + 1) : [];

            foreach (AttendanceType::all() as $type) {
                $data = [];

                foreach ($course->events ?? [] as $event) {
                    $data[] = $event->attendance->where('attendance_type_id', $type->id)->count();
                }

                $datasets[] = [
                    'label' => $type->name,
                    'data' => $data,
                    'backgroundColor' => $type->color,
                ];
            }
        }

        return view('reports.attendance.for-course', [
            'course' => $course ?? null,
            'otherCourses' => $otherCourses->get(),
            'chartData' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
            'selected_period' => ['type' => 'period', 'value' => $period->name],
        ]);
    }

    public function byCourse(Request $request)
    {
        $period = $this->selectPeriod($request);

        $courses = Course::wherePeriodId($period->id)->has('attendance')->has('events')->get();

        $datasets = [];

        $labels = $courses ? $courses->map(fn (Course $c) => $c->name) : [];

        foreach (AttendanceType::all() as $type) {
            $data = [];

            foreach ($courses ?? [] as $course) {
                $totalAttendanceRecords = $course->attendance->count();
                $data[] = round(100 * ($course->attendance->where('attendance_type_id', $type->id)->count() / $totalAttendanceRecords));
            }

            $datasets[] = [
                'label' => $type->name,
                'data' => $data,
                'backgroundColor' => $type->color,
            ];
        }

        return view('reports.attendance.by-course', [
            'selected_period' => ['type' => 'period', 'value' => $period->name],
            'chartData' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
        ]);
    }

    public function byLevel(Request $request)
    {
        $year = $this->selectYear($request);
        if ($year) {
            $selectedPeriod = ['type' => 'year', 'value' => $year->name];
            $periods = Period::whereYearId($year->id)->pluck('id');
            $courses = Course::whereIn('period_id', $periods);
        } else {
            $period = $this->selectPeriod($request);
            $selectedPeriod = ['type' => 'period', 'value' => $period->name];
            $courses = Course::wherePeriodId($period->id);
        }

        $groups = $courses
            ->has('attendance')
            ->has('events')
            ->get()
            ->groupBy('level_id')
            ->filter(fn ($g, $k) => Level::find($k) !== null);

        $datasets = [];

        $labels = $groups->map(fn ($g, $k) => 'Niveau '.Level::find($k)->name)->filter()->values();

        foreach (AttendanceType::all() as $type) {
            $data = [];

            foreach ($groups ?? [] as $group) {
                $groupTotalAttendanceRecords = 0;
                $groupTotalForType = 0;
                foreach ($group as $course) {
                    $groupTotalAttendanceRecords += $course->attendance->count();
                    $groupTotalForType += $course->attendance->where('attendance_type_id', $type->id)->count();
                }
                $data[] = round(100 * ($groupTotalForType / $groupTotalAttendanceRecords));
            }

            $datasets[] = [
                'label' => $type->name,
                'data' => $data,
                'backgroundColor' => $type->color,
                'fill' => false,
            ];
        }

        return view('reports.attendance.by-level', [
            'selected_period' => $selectedPeriod,
            'chartData' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
        ]);
    }

    public function byRhythm(Request $request)
    {
        $year = $this->selectYear($request);
        if ($year) {
            $selectedPeriod = ['type' => 'year', 'value' => $year->name];
            $periods = Period::whereYearId($year->id)->pluck('id');
            $courses = Course::whereIn('period_id', $periods);
        } else {
            $period = $this->selectPeriod($request);
            $selectedPeriod = ['type' => 'period', 'value' => $period->name];
            $courses = Course::wherePeriodId($period->id);
        }

        $groups = $courses
            ->has('attendance')
            ->has('events')
            ->get()
            ->groupBy('rhythm_id')
            ->filter(fn ($g, $k) => Rhythm::find($k) !== null);

        $datasets = [];

        $labels = $groups->map(fn ($g, $k) => Rhythm::find($k)->name)->filter()->values();

        foreach (AttendanceType::all() as $type) {
            $data = [];

            foreach ($groups ?? [] as $group) {
                $groupTotalAttendanceRecords = 0;
                $groupTotalForType = 0;
                foreach ($group as $course) {
                    $groupTotalAttendanceRecords += $course->attendance->count();
                    $groupTotalForType += $course->attendance->where('attendance_type_id', $type->id)->count();
                }
                $data[] = round(100 * ($groupTotalForType / $groupTotalAttendanceRecords));
            }

            $datasets[] = [
                'label' => $type->name,
                'data' => $data,
                'backgroundColor' => $type->color,
                'fill' => false,
            ];
        }

        return view('reports.attendance.by-rhythm', [
            'selected_period' => $selectedPeriod,
            'chartData' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
        ]);
    }
}
