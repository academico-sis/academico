<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_skill', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->integer('skill_id')->unsigned();
            $table->integer('weight')->nullable(); // specific weight of the skill in this course
            //$table->timestamps();
            //$table->softDeletes();
            $table->index(['course_id', 'skill_id']);
        });

        Schema::table('course_skill', function (Blueprint $table) {
            $table->foreign('course_id')
            ->references('id')->on('courses')
            ->onDelete('cascade');
        });

        Schema::table('course_skill', function (Blueprint $table) {
            $table->foreign('skill_id')
            ->references('id')->on('skills')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('course_skill');
    }
}
