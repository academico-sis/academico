<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMediaLibrary9 extends Migration
{
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->json('generated_conversions')->nullable();
        });
    }

    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            //
        });
    }
}
