<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Result;
use App\Models\ResultType;
use App\Models\Skills\SkillEvaluation;
use App\Models\Skills\SkillScale;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseSkillEvaluationController extends Controller
{
    /**
     * Show the skills overview for all students in the course.
     */
    public function index(Course $course)
    {
        if (Gate::forUser(backpack_user())->denies('view-course', $course)) {
            abort(403);
        }

        $skills = $course->skills->groupBy('skill_type_id');
        $enrollments = $course->enrollments()->with('skill_evaluations')->get();

        // get skill evaluations for the course
        $skill_evaluations = $course->skill_evaluations;

        return view('skills.students', compact('course', 'skills', 'skill_evaluations', 'enrollments'));
    }

    /**
     * Store a skill evaluation record for a student.
     */
    public function store(Request $request)
    {
        $skill = $request->input('skill');
        $status = $request->input('status');
        $enrollment = Enrollment::findOrFail($request->input('enrollment_id'));

        if (Gate::forUser(backpack_user())->denies('view-course', $enrollment->course)) {
            abort(403);
        }

        $new_skill = SkillEvaluation::firstOrNew([
            'enrollment_id' => $enrollment->id,
            'skill_id' => $skill,
        ]);

        $new_skill->skill_scale_id = $status;
        $new_skill->save();

        return $new_skill->skill_scale_id;
    }

    /**
     * Show the form for editing a specific student's skills for the specified course.
     */
    public function edit(Enrollment $enrollment)
    {
        if (Gate::forUser(backpack_user())->denies('view-enrollment', $enrollment)) {
            abort(403);
        }

        $student_skills = $enrollment->skill_evaluations;

        $course = Course::with('evaluationType')->find($enrollment->course_id);
        
        $skills = $course->skills->map(function ($skill, $key) use ($student_skills) {
            $skill['status'] = $student_skills->where('skill_id', $skill->id)->first()->skill_scale_id ?? null;
            return $skill;
        })->groupBy('skill_type_id');

        $result = Result::where(['enrollment_id' => $enrollment->id])->with('result_name')->first();

        $results = ResultType::all();
        $skillScales = SkillScale::orderBy('value')->get();
        $writeaccess = config('settings.teachers_can_edit_result') || backpack_user()->can('enrollments.edit') ?? 0;

        return view('skills.student', compact('enrollment', 'skills', 'skillScales', 'result', 'enrollment', 'results', 'writeaccess'));
    }
}
