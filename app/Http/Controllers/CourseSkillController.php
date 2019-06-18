<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Exports\UsersExport;
use App\Models\Skills\Skill;
use Illuminate\Http\Request;
use App\Exports\CoursesExport;

use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Imports\CourseSkillsImport;
use Maatwebsite\Excel\Facades\Excel;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;

class CourseSkillController extends Controller
{

    use Importable;

    public function __construct()
    {
        $this->middleware(['permission:evaluation.edit']);
    }

    /**
     * Display the specified course skills list.
     */
    public function index(Course $course)
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


    public function export(Course $course) 
    {
        return Excel::download(new CoursesExport($course), 'skills.xlsx');
    }


    public function import(Course $course) 
    {
        $course->skills()->detach();

        $skills = Excel::toArray(new CourseSkillsImport, 'skills.xlsx');

        foreach ($skills as $skill)
        {
            foreach($skill as $e)
            {
                $course->skills()->attach(Skill::find($e[0]),
                    ['weight' => 1]
                );
            }
        }

        return redirect('/')->with('success', 'All good!');
    }

}
