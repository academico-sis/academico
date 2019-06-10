<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Result;
use App\Models\Enrollment;
use App\Models\ResultType;
use Illuminate\Http\Request;
use App\Models\Skills\SkillScale;
use Illuminate\Support\Facades\Gate;
use App\Models\Skills\SkillEvaluation;

class CourseSkillEvaluationController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['permission:evaluation.edit']);
    }

    /**
     * Show the skills overview for all students in the course
     */
    public function index(Course $course)
    {

        if (Gate::forUser(backpack_user())->denies('view-course', $course)) {
            abort(403);
        }

        $skills = $course->skills;
        $enrollments = $course->enrollments;

        // get skill evaluations for the course
        $skill_evaluations = $course->skill_evaluations;

        return view('skills.students', compact('course', 'skills', 'skill_evaluations', 'enrollments'));

    }

    /**
     * Store a skill evaluation record for a student
     */
    public function store(Request $request)
    {

        $skill = $request->input('skill');
        $status = $request->input('status');
        $student = $request->input('student');
        $course = Course::findOrFail($request->input('course'));


        if (Gate::forUser(backpack_user())->denies('view-course', $course)) {
            abort(403);
        }

        $new_skill = SkillEvaluation::firstOrNew([
            'course_id' => $course->id,
            'student_id' => $student,
            'skill_id' => $skill,
        ]);

        $new_skill->skill_scale_id = $status;
        $new_skill->save();

        return $new_skill->skill_scale_id;
        }

    /**
     * Show the form for editing a specific student's skills for the specified course.
     */
    public function edit(Course $course, Student $student)
    {

        
        if (Gate::forUser(backpack_user())->denies('view-course', $course)) {
            abort(403);
        }
        
        $student_skills = SkillEvaluation::where('student_id', $student->id)
        ->where('course_id', $course->id)
        ->get();

        $skills = $course->skills->map(function ($skill, $key) use($student_skills) {
            $skill['status'] = $student_skills->where('skill_id', $skill->id)->first()->skill_scale_id ?? null;
            return $skill;
        });

        $enrollment = Enrollment::where('student_id', $student->id)
        ->where('course_id', $course->id)->first();

        $result = Result::where(['enrollment_id' => $enrollment->id])->with('result_name')->first();
        
        $results = ResultType::all();
        $skillScales = SkillScale::all();

        return view('skills.student', compact('course', 'student', 'skills', 'skillScales', 'result', 'enrollment', 'results'));
    }

}
