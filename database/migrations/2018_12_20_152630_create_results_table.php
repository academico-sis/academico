<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('enrollment_id')->unique()->unsigned();
            $table->integer('result_type_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('result_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description')->unique();
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
        Schema::dropIfExists('results');
        Schema::dropIfExists('result_types');

    }
}
