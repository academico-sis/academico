<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use App\Models\GradeType;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:evaluation.edit']);
    }

    /**
     * Show the form to edit grades for a course.
     *
     * Todo refactor to prevent the number of queries to depend upong the number of records
     */
    public function edit(Course $course)
    {
        $enrollments = $course->enrollments;
        $course_grade_types = $course->grade_type;
        $grades = $course->grades;
        $all_grade_types = GradeType::all();
        //return $grade_types;
        return view('grades.edit', compact('enrollments', 'course_grade_types', 'grades', 'course', 'all_grade_types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function store(Request $request)
    {
        $grade = Grade::findOrFail($request->input('pk'));
        $grade->grade = $request->input('value');
        $grade->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $grade = Grade::findOrFail($request->input('id'));
        $grade->delete();
    }

    public function add_grade_type_to_course(Request $request)
    {
        $course = Course::findOrFail($request->input('course_id'));
        $grade_type = GradeType::findOrFail($request->input('grade_type_id'));

        if (! $course->grade_type->has($grade_type->id)) {
            $course->grade_type()->attach($grade_type->id);
        }

        // todo improve -- create an empty grade for every student in course.
        foreach ($course->enrollments as $enrollment) {
            Grade::create([
                'student_id' => $enrollment->student_id,
                'grade_type_id' => $grade_type->id,
                'grade' => 0,
                'course_id' => $course->id,
            ]);
        }

        return redirect()->back();
    }

    public function remove_grade_type_from_course(Course $course, GradeType $gradetype)
    {
        $course->grade_type()->detach($gradetype->id);
        $course->grades()->where('grade_type_id', $gradetype->id)->delete();
    }
}
