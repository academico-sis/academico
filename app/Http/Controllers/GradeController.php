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
        
        $grades = $course->enrollments;

        dump($grades);

        return view('grades.edit', compact('grades'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Grade  $grade
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Grade $grade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Grade  $grade
     * @return \Illuminate\Http\Response
     */
    public function destroy(Grade $grade)
    {
        //
    }
}
