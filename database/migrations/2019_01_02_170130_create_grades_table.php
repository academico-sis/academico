<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grade_type_id')->unsigned();
            $table->integer('student_id')->unsigned(); // student
            $table->integer('course_id')->unsigned(); // relates the grade to the corresponding course
            $table->decimal('grade', 4, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->foreign('student_id')
            ->references('id')->on('students')
            ->onDelete('cascade');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->foreign('course_id')
            ->references('id')->on('courses')
            ->onDelete('cascade');
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

        Schema::dropIfExists('grades');
    }
}
