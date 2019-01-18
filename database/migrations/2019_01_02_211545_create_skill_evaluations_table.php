<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_evaluations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('skill_scale_id')->unsigned();
            $table->integer('skill_id')->unsigned();
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::table('skill_evaluations', function (Blueprint $table) {
            $table->foreign('course_id')
            ->references('id')->on('courses')
            ->onDelete('cascade');
        });

        Schema::table('skill_evaluations', function (Blueprint $table) {
            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onDelete('cascade');

            Schema::table('skill_evaluations', function (Blueprint $table) {
                $table->foreign('skill_id')
                ->references('id')->on('skills')
                ->onDelete('restrict');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_skill_evaluations');
    }
}
