<?php

namespace App\Http\Controllers;

use App\Models\Course;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class CourseSkillController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:evaluation.edit']);
    }

    public function exportCourseSyllabus(Course $course)
    {
        $phpWord = new PhpWord();

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

        foreach ($course->skills->sortBy('skill_type_id') as $skill) {
            if ($skill->level->name != $level) {
                $level = $skill->level->name;

                $section->addText('Niveau '.$level);
            }

            if ($skill->skillType->name != $type) {
                $type = $skill->skillType->name;

                $section->addText($type);
            }

            $section->addListItem($skill->name);
        }

        // Saving the document as OOXML file...
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        header('Content-type: application/msword');
        header('Cache-Control: no-store, no-cache');
        header('Content-Disposition: attachment; filename="document.docx"');

        $objWriter->save('php://output');
        exit;
    }
}
