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

        Schema::create('course_grade_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id');
            $table->integer('grade_type_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grade_types');
        Schema::dropIfExists('grade_type_id');
    }
}
