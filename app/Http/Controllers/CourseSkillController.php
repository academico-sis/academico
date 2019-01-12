<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Skill;

use Illuminate\Http\Request;

class CourseSkillController extends Controller
{


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CourseSkill  $courseSkill
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        $all_skills = Skill::all()->groupBy(['level_id', 'skill_type_id']);

        $course_skills = $course->skills;

        return view('skills.edit', compact('course', 'all_skills', 'course_skills'));
    }

}
