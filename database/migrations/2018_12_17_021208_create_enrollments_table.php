<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('enrollment_status_types', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
        });

        

        Schema::create('enrollments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->integer('responsible_id')->unsigned();
            $table->integer('course_id')->unsigned();
            $table->integer('status_id')->unsigned()->default(1);
            $table->integer('parent_id')->nullable()->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreign('student_id')
            ->references('id')->on('students')
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

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('enrollments');
    }
}
