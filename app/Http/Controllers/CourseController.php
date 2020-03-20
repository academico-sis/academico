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
        $this->middleware('permission:courses.view', ['except' => 'show']);
        $this->middleware('permission:courses.edit', ['only' => ['update', 'create', 'store', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $defaultPeriod = Period::get_default_period();
        $rhythms = Rhythm::all();
        $levels = Level::all();
        $isAllowedToEdit = backpack_user()->hasPermissionTo('courses.edit');

        return view('courses.list', compact('defaultPeriod', 'isAllowedToEdit', 'rhythms', 'levels'));
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
