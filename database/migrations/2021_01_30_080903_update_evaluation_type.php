<?php

use App\Models\Course;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEvaluationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('evaluation_type_id')
                ->references('id')->on('evaluation_types')
                ->onDelete('restrict')
                ->after('color')
                ->nullable();
        });

        // We need to migrate the old course_evaluation_type contents to this new format.
        foreach (DB::table('course_evaluation_type')->get() as $course_eval)
        {
            Course::find($course_eval->course_id)->update(['evaluation_type_id' => $course_eval->evaluation_type_id]);
        }

        // then, you can delete the course_evaluation_type table

        // You also need to create new evaluation types to reconstruct former courses skills and gradetypes associations.
        foreach (DB::table('course_grade_type')->get() as $entry)
        {
            // collect all grade types associated to this course.
            $courseGradeTypes = DB::table('course_grade_type')->where('course_id', $entry->course_id)->pluck('grade_type_id');

            // if this configuration matches an evaluation type, use it, otherwise create it
            foreach (EvaluationType::has('gradeTypes')->get() as $evaluationType) {
                if ($courseGradeTypes->diff($evaluationType->gradeTypes->pluck('id'))->count() == 0) {
                    Course::find($entry->course_id)->update(['evaluation_type_id' => $evaluationType->id]);
                }
            }

            // if no match has been found
            if (Course::find($entry->course_id)->evaluation_type_id == null) {
                $evalType = EvaluationType::create(['name' => 'cours ' . $entry->course_id]);
                $evalType->gradeTypes()->sync($courseGradeTypes);
            }
        }

        // then you can delete course_grade_type manually

        // same process for skills
        foreach (DB::table('course_skill')->get() as $entry)
        {
            // collect all grade types associated to this course.
            $courseSkills = DB::table('course_skill')->where('course_id', $entry->course_id)->pluck('skill_id');

            // if this configuration matches an evaluation type, use it, otherwise create it
            foreach (EvaluationType::has('skills')->get() as $evaluationType) {
                if ($courseSkills->diff($evaluationType->skills->pluck('id'))->count() == 0) {
                    Course::find($entry->course_id)->update(['evaluation_type_id' => $evaluationType->id]);
                }
            }

            // if no match has been found
            if (Course::find($entry->course_id)->evaluation_type_id == null) {
                $evalType = EvaluationType::create(['name' => 'Niveau ' . Course::find($entry->course_id)->level->name]);
                $evalType->skills()->sync($courseSkills);
            }
        }

        // then you can delete course_skill manually

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
