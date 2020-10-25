<?php

namespace App\Http\Controllers;

use App\Exports\CoursesExport;
use App\Imports\CourseSkillsImport;
use App\Models\Course;
use App\Models\Skills\Skill;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Facades\Excel;

class CourseSkillController extends Controller
{
    use Importable;

    public function __construct()
    {
        $this->middleware(['permission:evaluation.edit']);
    }

    protected function getCoursesSkillList($course)
    {
        return $course->skills->groupBy('skill_type_id')->toArray();
    }

    public function exportCourseSyllabus(Course $course)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Course general info
        $section = $phpWord->addSection();

        $section->addText('Cours : '.$course->name);

        $section->addText('Session : '.$course->period->name);

        $section->addText('Enseignant(e) : '.$course->teacher->name);

        $section->addText('Dates du cours : '.$course->start_date->format('d/m').' - '.$course->end_date->format('d/m'));

        $section->addTextBreak();

        // Course skills
        $level = '';
        $type = '';

        foreach ($course->skills->sortBy('skill_type_id') as $s => $skill) {
            if ($skill->level->name != $level) {
                $level = $skill->level->name;

                $section->addText('Niveau '.$level);
            }

            if ($skill->skill_type->name != $type) {
                $type = $skill->skill_type->name;

                $section->addText($type);
            }

            $section->addListItem($skill->name);
        }

        // Saving the document as OOXML file...
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        header('Content-type: application/msword');
        header('Cache-Control: no-store, no-cache');
        header('Content-Disposition: attachment; filename="document.docx"');

        $objWriter->save('php://output');
        exit;
    }

    /**
     * Display the specified course skills list.
     */
    public function index(Course $course)
    {
        return view('skills.course', compact('course'));
    }

    // get skills for the selected course level which are not already assigned to the course
    public function getAvailableSkills(Course $course)
    {
        return Skill::where('level_id', $course->level_id)->whereNotIn('id', $course->refresh()->skills->pluck('id'))->get()->groupBy('skill_type_id')->toJson();
    }

    public function getCourseSkills(Course $course)
    {
        return $this->getCoursesSkillList($course);
    }

    public function addSkill(Course $course, Request $request)
    {
        $request->validate(['skill_id' => 'required']);

        if ($request->skill_id == 'all') {
            foreach (Skill::where('level_id', $course->level_id)->whereNotIn('id', $course->refresh()->skills->pluck('id'))->get() as $skill) {
                $course->skills()->attach($skill, ['weight' => 1]);
            }
        } else {
            $course->skills()->attach(Skill::find($request->skill_id), ['weight' => 1]);
        }

        return $this->getCoursesSkillList($course->refresh());
    }

    public function removeSkill(Course $course, Request $request)
    {
        $request->validate(['skill_id' => 'required']);

        if ($request->skill_id == 'all') {
            $course->skills()->detach();
        } else {
            $course->skills()->detach(Skill::find($request->skill_id));
        }

        return $this->getCoursesSkillList($course);
    }

    public function set(Course $course, Request $request)
    {
        // TODO: Review this method
        foreach ($request->skills as $skill) {
            $s = Skill::find($skill['id']);
            $s->order = $skill['order'];
            $s->save();
        }
    }

    public function export(Course $course)
    {
        return Excel::download(new CoursesExport($course), 'skills.xlsx');
    }

    public function import(Course $course, Request $request)
    {
        if (! $request->hasFile('skillset')) {
            abort(422, 'No file has been uploaded');
        }

        $course->skills()->detach();

        $skills = Excel::toArray(new CourseSkillsImport, $request->file('skillset'));

        foreach ($skills as $skill) {
            foreach ($skill as $e) {
                $course->skills()->attach(Skill::find($e[0]),
                    ['weight' => 1]
                );
            }
        }

        return redirect()->back();
    }
}
