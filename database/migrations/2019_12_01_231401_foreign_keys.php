<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Schema::table('courses', function (Blueprint $table) {
            $table->foreign('campus_id')
            ->references('id')->on('campuses')
            ->onDelete('restrict');

            $table->foreign('rythm_id')
            ->references('id')->on('rythms')
            ->onDelete('restrict');
            
            $table->foreign('level_id')
            ->references('id')->on('levels')
            ->onDelete('restrict');

            $table->foreign('room_id')
            ->references('id')->on('rooms')
            ->onDelete('restrict');

            $table->foreign('teacher_id')
            ->references('id')->on('users')
            ->onDelete('restrict');

            $table->foreign('parent_course_id')
            ->references('id')->on('courses')
            ->onDelete('cascade');

            $table->foreign('eval_type')
            ->references('id')->on('evaluation_types')
            ->onDelete('restrict');

            $table->foreign('period_id')
            ->references('id')->on('periods')
            ->onDelete('restrict');
        });


        Schema::table('periods', function (Blueprint $table) {
            $table->foreign('year_id')
            ->references('id')->on('years')
            ->onDelete('restrict');
        });


        Schema::table('events', function (Blueprint $table) {
            $table->foreign('course_id')
            ->references('id')->on('courses')
            ->onDelete('cascade');

            $table->foreign('teacher_id')
            ->references('id')->on('users')
            ->onDelete('restrict');

            $table->foreign('room_id')
            ->references('id')->on('rooms')
            ->onDelete('restrict');

            $table->foreign('course_time_id')
            ->references('id')->on('course_times')
            ->onDelete('cascade');
        });


        Schema::table('course_times', function (Blueprint $table) {
            $table->foreign('course_id')
            ->references('id')->on('courses')
            ->onDelete('cascade');
        });


        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onDelete('restrict');

            $table->foreign('responsible_id')
            ->references('id')->on('users')
            ->onDelete('restrict');

            $table->foreign('course_id')
            ->references('id')->on('courses')
            ->onDelete('restrict');

            $table->foreign('parent_id')
            ->references('id')->on('enrollments')
            ->onDelete('cascade');
        });


        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('author_id')
            ->references('id')->on('users')
            ->onDelete('restrict');
        });

        Schema::table('results', function (Blueprint $table) {
            $table->foreign('result_type_id')
            ->references('id')->on('result_types')
            ->onDelete('restrict');

            $table->foreign('enrollment_id')
            ->references('id')->on('enrollments')
            ->onDelete('cascade');

        $table->foreign('status_id')
        ->references('id')->on('enrollment_status_types')
        ->onDelete('restrict');
        });


        Schema::table('rules', function (Blueprint $table) {
            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onDelete('cascade');
        }); */

            // todo preinvoice

            // todo rules

            // todo attendances

            // ... and following migrations ...
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
