<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Period;
use App\Models\Campus;

class CourseController extends Controller
{

    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Period $period)
    {
        if (!$period->exists) {
            $period = Period::get_default_period();
        }

        $courses = (new Course)->get_all_internal_courses($period);
        return view('courses/index', compact('courses', 'period'));   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rythms = \App\Models\Rythm::all();
        $levels = \App\Models\Level::all();

        return view('courses/create', compact('rythms', 'levels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $course = new \App\Models\Course;
        $course->period_id = $request->input('period');
        $course->start_date = $request->input('start');
        $course->end_date = $request->input('end');
        $course->rythm_id = $request->input('rythm');
        $course->level_id = $request->input('level');
        $course->name = $request->input('name');
        $course->volume = $request->input('volume');
        $course->price = $request->input('price');
        $course->save();

        // redirect to teacher edit -- for now (todo)
        return redirect("/course/$course->id/teacher");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        $students = \App\Models\Enrollment::show($course);
        return view('courses/show', compact('course', 'students'));   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $course)
    {
        // todo protect the method
        Course::destroy($course->input('id'));
        // todo return confirmation       
    }
}
