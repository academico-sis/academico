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
            $table->string('name')->unique();
            $table->string('description')->nullable();

            //$table->timestamps();
        });

        DB::table('result_types')->insert(
            array(
                'id' => 1,
                'name' => 'VALIDE',
                'description' => 'Peut passer au niveau suivant'
                )
        );

        DB::table('result_types')->insert(
            array(
                'id' => 2,
                'name' => 'NON VALIDE',
                'description' => 'Ne peut pas passer au niveau suivant'
                )
        );

        DB::table('result_types')->insert(
            array(
                'id' => 3,
                'name' => 'SOUS CONDITIONS',
                'description' => 'Voir avec le département Pédagogique'
                )
        );
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
