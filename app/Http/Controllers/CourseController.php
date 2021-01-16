<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Student;
use App\Traits\FiltersSearchableLevels;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CourseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:courses.view');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $defaultPeriod = Period::get_default_period();
        $rhythms = Rhythm::all();
        $levels = Level::all();
        $isAllowedToEdit = backpack_user()->hasPermissionTo('courses.edit') ? 1 : 0;
        $mode = $request->mode ?? 'view';
        $student = Student::with('enrollments')->find($request->student_id) ?? collect(['']);
        $enrollment_id = $request->enrollment_id ?? 'none';

        return view('courses.list', compact('defaultPeriod', 'isAllowedToEdit', 'rhythms', 'levels', 'mode', 'student', 'enrollment_id'));
    }

    public function search()
    {
        return QueryBuilder::for(Course::class)->where('campus_id', 1)
        ->with('room')->withCount('events')->withCount('children')->withCount('enrollments')
        ->allowedFilters([
            'name',
            'period_id',
            'rhythm_id',
            AllowedFilter::custom('searchable_levels', new FiltersSearchableLevels()),
            'teacher_id', ])
        ->get();
    }

    public function redirectToUserPreferredView()
    {
        switch (backpack_user()->preferred_course_view) {
            case 'blocks':
                return redirect(route('get-courses-list'));

            default:
                return redirect(route('course.index'));
        }
    }

    public function switchViews(Request $request)
    {
        switch ($request->view) {
            case 'blocks':
                backpack_user()->update(['preferred_course_view' => 'blocks']);

                return redirect(route('get-courses-list'));

            default:
                backpack_user()->update(['preferred_course_view' => 'list']);

                return redirect(route('course.index'));
        }
    }
}
