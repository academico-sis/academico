<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDataRelationships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_data_relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            //$table->timestamps();
        });

        DB::table('user_data_relationships')->insert(
            array(
                'id' => 1,
                'name' => 'FAMILLE'
            )
        );

        DB::table('user_data_relationships')->insert(
            array(
                'id' => 2,
                'name' => 'ENTREPRISE'
            )
        );

        Schema::table('user_data', function (Blueprint $table) {
            $table->foreign('relationship_id')
            ->references('id')->on('user_data_relationships')
            ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_data_relationships');
    }
}
