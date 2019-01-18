<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Result;
use App\Models\Enrollment;
use App\Models\ResultType;
use Illuminate\Http\Request;
use App\Models\SkillEvaluation;
use App\Models\CourseSkillEvaluation;

class CourseSkillEvaluationController extends Controller
{


    /**
     * Show the skills overview for all students in the course
     */
    public function index(Course $course)
    {
        $this->middleware(['permission:grades.edit']);

        $skills = $course->skills;
        $students = $course->enrollments;

        return view('skills.students', compact('course', 'skills', 'students'));
    }

    /**
     * Store a skill evaluation record for a student
     */
    public function store(Request $request)
    {
        $this->middleware(['permission:grades.edit']);

        $skill = $request->input('skill');
        $status = $request->input('status');
        $student = $request->input('student');
        $course = $request->input('course');

        $new_skill = SkillEvaluation::firstOrNew([
            'course_id' => $course,
            'user_id' => $student,
            'skill_id' => $skill,
        ]);

        $new_skill->skill_scale_id = $status;
        $new_skill->save();
        }

    /**
     * Show the form for editing a specific student's skills for the specified course.
     */
    public function edit(Course $course, User $student)
    {
        $this->middleware(['permission:grades.edit']);

        $student_skills = SkillEvaluation::where('user_id', $student->id)
        ->where('course_id', $course->id)
        ->get();

        $skills = $course->skills->map(function ($skill, $key) use($student_skills) {
            $skill['status'] = $student_skills->where('skill_id', $skill->id)->first()->skill_scale_id ?? null;
            return $skill;
        });

        $course_result = Enrollment::where('user_id', $student->id)
        ->where('course_id', $course->id)
        ->first()->result;
        
        $results = ResultType::all();

        $comments = $course_result->comments;

        return view('skills.student', compact('course', 'student', 'skills', 'comments', 'course_result', 'results'));
    }

}
