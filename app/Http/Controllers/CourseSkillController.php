<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Skills\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;

class CourseSkillController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:evaluation.edit']);
    }

    /**
     * Display the specified course skills list.
     */
    public function show(Course $course)
    {
        $skills = $course->skills->toJson();

        return view('skills.course', compact('course', 'skills'));
    }

    public function get(Course $course)
    {
        return $course->skills->toJson();
    }

    public function set(Course $course, Request $request)
    {
        //DB::delete('course_skill')->where('course_id', $course->id);

        foreach($request->skills as $skill)
        {
            $s = Skill::find($skill['id']);
            $s->order = $skill['order'];
            $s->save();
        }
        //return $course->skills->toJson();
    }

}
