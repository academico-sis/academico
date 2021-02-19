<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTitlesTable extends Migration
{
    public function up()
    {
        Schema::create('titles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->unique();
        });
    }

    public function down()
    {
        Schema::dropIfExists('titles');
    }
}
