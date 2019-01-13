<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\Course;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

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
        $course_skills = $course->skills;
        //return $course_skills;
        // filter here (by level)
        $all_skills = Skill::with('skill_type')->with('level')->get();
        $skills = $all_skills->map(function ($item, $key) use($course_skills) {
            $item['attached'] = false;
            if ($course_skills->has($item->id)) {
                $item['attached'] = true;
            }
            return $item;
        });
        //dd($skills);
        return view('skills.edit', compact('course', 'skills'));
    }

    public function update(Course $course, Request $request)
    {
        $course->skills()->detach();
        foreach ($request->input('skill') as $key => $skill)
        {
            $course->skills()->attach(Skill::find($key),
            ['weight' => 1]);
        }
        Alert::success('Skills set was saved for the course')->flash();

        return back();
    }

}
