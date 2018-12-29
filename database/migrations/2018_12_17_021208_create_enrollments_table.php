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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); // todo user or student ?
            $table->integer('responsible_id')->unsigned();
            $table->integer('course_id')->unsigned();
            $table->integer('invoice_id')->nullable()->unsigned(); // todo add FK constrain
            $table->integer('status')->unsigned()->default(1); // todo add table. 1 = pending
            $table->text('comment')->nullable(); // todo move to global comments table
            $table->integer('parent_id')->nullable()->unsigned();
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
        Schema::dropIfExists('enrollments');
    }
}
