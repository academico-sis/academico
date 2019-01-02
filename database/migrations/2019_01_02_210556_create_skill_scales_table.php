<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillScalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_scales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shortname');
            $table->string('name')->nullable();
            $table->decimal('value', 2, 1); // decimal number between 0 and 1
            $table->timestamps();
            $table->softDeletes(); 
        });

        DB::table('skill_scales')->insert(
            array(
                'id' => 1,
                'shortname' => 'NO',
                'name' => 'NON-ACQUIS',
                'value' => 0
            )
        );

        DB::table('skill_scales')->insert(
            array(
                'id' => 2,
                'shortname' => 'PR',
                'name' => 'EN COURS',
                'value' => 0
            )
        );

        DB::table('skill_scales')->insert(
            array(
                'id' => 3,
                'shortname' => 'SI',
                'name' => 'ACQUIS',
                'value' => 1
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
        Schema::dropIfExists('skill_scales');
    }
}
