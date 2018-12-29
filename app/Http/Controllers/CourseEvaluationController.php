<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseEvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function edit(Course $course)
    {
        $evaluation_types = \App\Models\EvaluationType::all();
        return view('courses/create5', compact('course', 'evaluation_types'));
        // after refactor with VueSJS, no need for additionnal views
    }
 
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $course_id = $request->input('course_id');
        $course = \App\Models\Course::findOrFail($course_id);
        $course->evaluation_type_id = $request->input('evaluation_type');
        $course->save();
        return redirect('/courses');
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
