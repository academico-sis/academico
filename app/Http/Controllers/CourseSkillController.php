<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\Course;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

class CourseSkillController extends Controller
{


    /**
     * Display the specified course skills list.
     */
    public function edit(Course $course)
    {
        $this->middleware(['permission:grades.edit']);

        $course_skills = $course->skills;

        // todo - filter here (by level/type)?
        $all_skills = Skill::with('skill_type')->with('level')->get();

        $skills = $all_skills->map(function ($item, $key) use($course_skills) {
            $item['attached'] = false;
            if ($course_skills->has($item->id)) {
                $item['attached'] = true;
            }
            return $item;
        });

        return view('skills.course', compact('course', 'skills'));
    }

    public function update(Course $course, Request $request)
    {
        $this->middleware(['permission:grades.edit']);

        $course->skills()->detach();
        foreach ($request->input('skill') as $key => $skill)
        {
            $course->skills()->attach(Skill::find($key),
            ['weight' => 1]); // todo allow edition of this parameter
        }
        Alert::success('Skills set was saved for the course')->flash();

        return back();
    }

}
