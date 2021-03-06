<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultColumnToFees extends Migration
{
    public function up()
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->boolean('default')->default(false);
        });
    }

    public function down()
    {
        Schema::table('fees', function (Blueprint $table) {
            //
        });
    }
}
