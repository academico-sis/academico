<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            //$table->timestamps();
            $table->softDeletes();
        });

        DB::table('campuses')->insert(
            array(
                'id' => 1,
                'name' => 'Interne'
            )
        );
        DB::table('campuses')->insert(
            array(
                'id' => 2,
                'name' => 'Externe'
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
        Schema::dropIfExists('campuses');
    }
}
