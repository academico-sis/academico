<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); // todo - check - or use polymorphism?
            $table->timestamp('hired_at')->nullable();
            $table->decimal('max_week_hours', 4, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('teacher_id')
            ->references('id')->on('teachers')
            ->onDelete('restrict');
        });

        Schema::table('remote_events', function (Blueprint $table) {
            $table->foreign('teacher_id')
            ->references('id')->on('teachers')
            ->onDelete('cascade');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->foreign('teacher_id')
            ->references('id')->on('teachers')
            ->onDelete('cascade');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreign('teacher_id')
            ->references('id')->on('teachers')
            ->onDelete('restrict');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreign('user_id')
            ->references('id')->on('users')
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

        Schema::dropIfExists('teachers');
    }
}
