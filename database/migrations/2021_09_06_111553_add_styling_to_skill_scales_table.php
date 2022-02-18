<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('skill_scales', function (Blueprint $table) {
            $table->string('classes')->nullable();
        });
    }

    public function down()
    {
        Schema::table('skill_scales', function (Blueprint $table) {
            //
        });
    }
};
