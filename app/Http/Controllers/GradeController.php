<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Course;

use Illuminate\Http\Request;

class GradeController extends Controller
{

    /**
     * Show the form to edit grades for a course
     * 
     * Todo refactor to prevent the number of queries to depend upong the number of records
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        
        $enrollments = $course->enrollments;

        return view('grades.edit', compact('enrollments'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Grade  $grade
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $grade = Grade::findOrFail($request->input('pk'));
        $grade->grade = $request->input('value');
        $grade->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Grade  $grade
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $grade = Grade::findOrFail($request->input('id'));
        $grade->delete();
    }
}
