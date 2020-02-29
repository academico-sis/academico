<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
        });

        Schema::create('leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->unsigned();
            $table->date('date');
            $table->integer('leave_type_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->foreign('leave_type_id')
            ->references('id')->on('leave_types')
            ->onDelete('restrict');
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

        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('leaves');
    }
}
