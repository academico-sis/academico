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
            'available_grade_types' => GradeType::whereNotIn('id', $course->grade_types->pluck('id')->toArray())->get(),
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

    public function addGradeTypeToCourse(Request $request)
    {
        $course = Course::findOrFail($request->input('course_id'));

        $this->checkAccessForCourse($course);

        $gradeType = GradeType::findOrFail($request->input('grade_type_id'));

        if (! $course->grade_types->contains($gradeType->id)) {
            $course->grade_types()->attach($gradeType->id);
        }

        return redirect()->back();
    }

    public function removeGradeTypeFromCourse(Course $course, GradeType $gradetype)
    {
        $this->checkAccessForCourse($course);

        $course->grade_types()->detach($gradetype->id);
        $course->grades()->where('grade_type_id', $gradetype->id)->delete();
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
