<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
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
            $table->integer('course_id')->nullable();
            $table->integer('teacher_id')->nullable();
            $table->integer('room_id')->nullable();
            $table->datetime('start');
            $table->datetime('end');
            $table->string('name');
            $table->integer('course_time_id')->nullable();
            $table->boolean('exempt_attendance')->nullable();
            $table->softDeletes();
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
