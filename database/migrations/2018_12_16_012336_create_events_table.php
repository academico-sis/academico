<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->nullable()->unsigned(); //! Events should be related to coursetime only.
            $table->integer('teacher_id')->nullable()->unsigned();
            $table->integer('room_id')->nullable()->unsigned();
            $table->datetime('start');
            $table->datetime('end');
            $table->string('name');
            $table->integer('course_time_id')->nullable()->unsigned();
            $table->boolean('exempt_attendance')->nullable();
            //$table->softDeletes();
            $table->timestamps();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreign('course_id')
            ->references('id')->on('courses')
            ->onDelete('cascade');

            $table->foreign('room_id')
            ->references('id')->on('rooms')
            ->onDelete('restrict');

            $table->foreign('course_time_id')
            ->references('id')->on('course_times')
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
        Schema::dropIfExists('events');
    }
};
