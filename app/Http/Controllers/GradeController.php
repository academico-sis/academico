<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GradeController extends Controller
{
    /**
     * Show the form to edit grades for a course.
     *
     * Todo refactor to prevent the number of queries to depend upon the number of records
     */
    public function edit(Course $course)
    {
        $this->checkAccessForCourse($course);

        return view('grades.edit', [
            'enrollments' => $course->enrollments,
            'course_grade_types' => $course->grade_types->sortBy('id'),
            'grades' => $course->grades,
            'course' => $course,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function store(Request $request)
    {
        $enrollment = Enrollment::findOrFail($request->input('enrollment_id'));
        $this->checkAccessForCourse($enrollment->course);
        $grade = Grade::firstOrNew([
            'grade_type_id' => $request->input('grade_type_id'),
            'enrollment_id' => $request->input('enrollment_id'),
        ]);
        $grade->grade = $request->input('value');
        $grade->save();
    }

    public function getEnrollmentTotal(Request $request)
    {
        $enrollment = Enrollment::findOrFail($request->input('enrollment_id'));
        $this->checkAccessForCourse($enrollment->course);

        return $enrollment->grades->sum('grade');
    }

    protected function checkAccessForCourse(Course $course): void
    {
        if (Gate::forUser(backpack_user())->denies('edit-course-grades', $course)) {
            abort(403);
        }
    }
}
