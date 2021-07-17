<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\EvaluationType;
use App\Models\Course;

class migrateGrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grades:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (DB::table('course_grade_type')->get() as $entry)
        {
            $course = Course::find($entry->course_id);
            $matchFound = false;

            echo "\nMigrating grades from course " . $course->id;

            // collect all grade types associated to this course.
            $courseGradeTypes = DB::table('course_grade_type')->where('course_id', $entry->course_id)->pluck('grade_type_id');
            // if this configuration matches an evaluation type, use it, otherwise create it
            foreach (EvaluationType::has('gradeTypes')->get() as $evaluationType) {
                if ($courseGradeTypes->diff($evaluationType->gradeTypes->pluck('id'))->count() == 0) {
                    echo "match with eval type " . $evaluationType->id;
                    $course->update(['evaluation_type_id' => $evaluationType->id]);
                    $matchFound = true;
                }
            }

            // if no match has been found
            if (!$matchFound) {
                echo "no match; creating new eval type";
                $evalType = EvaluationType::create(['name' => 'cours ' . $entry->course_id]);
                $evalType->gradeTypes()->sync($courseGradeTypes);
            }

            echo "\n";
        }

        
        foreach (DB::table('course_skill')->get() as $entry)
        {
            $course = Course::find($entry->course_id);
            $matchFound = false;

            echo "\nMigrating skills from course " . $course->id;

            // collect all grade types associated to this course.
            $courseSkills = DB::table('course_skill')->where('course_id', $entry->course_id)->pluck('skill_id');

            // if this configuration matches an evaluation type, use it, otherwise create it
            foreach (EvaluationType::has('skills')->get() as $evaluationType) {
                if ($courseSkills->diff($evaluationType->skills->pluck('id'))->count() == 0) {
                    $course->update(['evaluation_type_id' => $evaluationType->id]);
                    $matchFound = true;
                }
            }

            // if no match has been found
            if (!$matchFound) {
                $levelName = isset($course->level) ? $course->level->name : $course->id; 
                $evalType = EvaluationType::create(['name' => 'Niveau ' . $levelName]);
                $evalType->skills()->sync($courseSkills);
            }
        }
    }
}
