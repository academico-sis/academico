<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLmsSyncColumnToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('sync_to_lms')->nullable();
            $table->bigInteger('lms_id')->nullable();
        });

        Schema::table('rhythms', function (Blueprint $table) {
            $table->bigInteger('lms_id')->nullable();
        });

        Schema::table('levels', function (Blueprint $table) {
            $table->bigInteger('lms_id')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('lms_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            //
        });
    }
}
