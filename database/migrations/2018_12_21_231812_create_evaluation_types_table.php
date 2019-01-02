<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            //$table->timestamps();
        });

        DB::table('evaluation_types')->insert(
            array(
                'id' => 1,
                'name' => 'NOTES'
            )
        );

        DB::table('evaluation_types')->insert(
            array(
                'id' => 2,
                'name' => 'COMPÉTENCES'
            )
        );

        Schema::create('course_evaluation_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->integer('evaluation_type_id')->unsigned();
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
        Schema::dropIfExists('evaluation_types');
    }
}
