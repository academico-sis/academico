<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class FiltersSearchableLevels implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $value = collect($value)->toArray();
        $query->where(function (Builder $query) use ($value) {
            $query->whereIn('level_id', $value)
                    ->orWhereHas('children', function (Builder $query) use ($value) {
                        $query->whereIn('level_id', $value);
                    });
        });
    }
}

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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $defaultPeriod = Period::get_default_period();
        $rhythms = Rhythm::all();
        $levels = Level::all();
        $isAllowedToEdit = backpack_user()->hasPermissionTo('courses.edit') ? 1 : 0;
        $mode = $request->mode ?? 'view';
        $student_id = $request->student_id ?? 'none';
        $enrollment_id = $request->enrollment_id ?? 'none';

        return view('courses.list', compact('defaultPeriod', 'isAllowedToEdit', 'rhythms', 'levels', 'mode', 'student_id', 'enrollment_id'));
    }

    public function search()
    {
        return QueryBuilder::for(Course::class)->where('campus_id', 1)
        ->with('room')->withCount('events')->withCount('children')
        ->allowedFilters([
            'name',
            'period_id',
            'rhythm_id',
            AllowedFilter::custom('searchable_levels', new FiltersSearchableLevels),
            'teacher_id', ])
        ->get();
    }
}
