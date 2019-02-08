<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade_types', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->integer('total');
            $table->timestamps();
            $table->softDeletes();
        });

        // this table means to display all grade types attached to the course
        // however, the students' records are related to the 'grade' model.
        Schema::create('course_grade_type', function (Blueprint $table) {
            $table->increments('id'); // todo delete this column?
            $table->integer('course_id')->unsigned();
            $table->integer('grade_type_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->foreign('grade_type_id')
            ->references('id')->on('grade_types')
            ->onDelete('restrict');
        });

        Schema::table('course_grade_type', function (Blueprint $table) {
            $table->foreign('course_id')
            ->references('id')->on('courses')
            ->onDelete('cascade');

            $table->foreign('grade_type_id')
            ->references('id')->on('grade_types')
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

        Schema::dropIfExists('grade_types');
        Schema::dropIfExists('grade_type_id');
    }
}
