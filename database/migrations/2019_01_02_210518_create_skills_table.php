<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->integer('default_weight')->default(1);
            $table->integer('level_id')->unsigned();
            $table->integer('skill_type_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->foreign('level_id')
            ->references('id')->on('levels')
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

        Schema::dropIfExists('skills');
    }
}
