<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRemoteEvents extends Migration
{
    public function up()
    {
        Schema::table('remote_events', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable();

            $table->integer('teacher_id')->unsigned()->nullable()->change();
            $table->integer('period_id')->unsigned()->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('remote_events', function (Blueprint $table) {
            //
        });
    }
}
