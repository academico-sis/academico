<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Grade;
use App\Models\GradeType;
use App\Models\EvaluationType;

use App\Models\Course;

use Illuminate\Support\Facades\DB;

class MigrateGrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datamigration:grades';

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
     * @return mixed
     */
    public function handle()
    {
        // retrieve the old grades
        $grades = DB::table('afc2.grades')
        ->select(DB::raw('id, id_tipo_eval, id_curso'))
        ->get();

        $graded_courses = [];
        $grade_types = [];

        // for each grade
        foreach ($grades as $grade)
        {
            if (Course::where('id', $grade->id_curso)->count() > 0) {
                $course = Course::findOrFail($grade->id_curso);
                if(!$course->grade_type->contains($grade->id_tipo_eval)) {
                    $course->grade_type()->attach($grade->id_tipo_eval);
                }
/*                 $course->grade_type()->firstOrCreate([
                    'course_id' => $grade->id_curso,
                    'grade_type_id' => $grade->id_tipo_eval
                ]); */
                
                // attach the 'grade' evaluation type to the course
                //$course->evaluation_type()->attach(1);
                array_push($graded_courses, $grade->id_curso);
            }
        }

        $graded_courses = array_unique($graded_courses);
        // attach the grade types to the courses
        $gradetype = EvaluationType::find(1);
        $gradetype->courses()->sync($graded_courses);


        // attach the skill eval type to all corresponding courses

        $skilled_courses = DB::table('afc2.courses')
        ->select(DB::raw('id, id_eval'))
        ->where('id_eval', 2)
        ->get();

        foreach ($skilled_courses as $course)
        {
            $course = Course::findOrFail($course->id);
            $course->evaluation_type()->attach(2);
        }
    }
}

