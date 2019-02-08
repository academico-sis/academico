<?php

namespace App\Http\Controllers;

use App\Models\Skills\Skill;
use App\Models\Course;
use Illuminate\Http\Request;
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
        $skills = $course->skills;

        return view('skills.course', compact('course', 'skills'));
    }

}
