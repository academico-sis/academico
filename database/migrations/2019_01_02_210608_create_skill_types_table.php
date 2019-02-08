<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shortname');
            $table->string('name')->nullable();
            $table->timestamps();
            $table->softDeletes(); 

        });

        Schema::table('skills', function (Blueprint $table) {
            $table->foreign('skill_type_id')
            ->references('id')->on('skill_types')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('skill_types');
    }
}
