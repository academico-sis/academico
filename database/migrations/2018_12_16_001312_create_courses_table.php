<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campus_id')->default(1)->unsigned();
            $table->integer('rhythm_id')->nullable()->unsigned();
            $table->integer('level_id')->nullable()->unsigned();
            $table->integer('volume')->nullable();
            $table->string('name');
            $table->bigInteger('price')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('room_id')->unsigned()->nullable();
            $table->integer('teacher_id')->unsigned()->nullable();
            $table->integer('parent_course_id')->unsigned()->nullable();
            $table->boolean('exempt_attendance')->nullable();
            $table->integer('period_id')->unsigned();
            $table->integer('spots')->nullable();
            //$table->softDeletes();
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('parent_course_id')
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
        Schema::dropIfExists('courses');
    }
};
