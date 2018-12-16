<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campus_id')->default(1);
            $table->integer('rythm_id')->nullable();
            $table->integer('level_id')->nullable();
            $table->integer('volume')->default(0);
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('room_id')->nullable();
            $table->integer('teacher_id')->nullable();
            $table->integer('parent_course')->nullable();
            $table->integer('eval_type')->nullable();
            $table->boolean('exempt_attendance')->nullable();
            $table->integer('period_id');
            $table->boolean('opened')->nullable();
            $table->integer('spots')->nullable();
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
        Schema::dropIfExists('courses');
    }
}
