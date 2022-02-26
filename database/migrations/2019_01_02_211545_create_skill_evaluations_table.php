<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_evaluations', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('enrollment_id')->unsigned();
            $table->integer('skill_scale_id')->unsigned();
            $table->integer('skill_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('skill_evaluations', function (Blueprint $table) {
            $table->foreign('enrollment_id')
            ->references('id')->on('enrollments')
            ->onDelete('cascade');

            Schema::table('skill_evaluations', function (Blueprint $table) {
                $table->foreign('skill_id')
                ->references('id')->on('skills')
                ->onDelete('restrict');
            });
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

        Schema::dropIfExists('skill_evaluations');
    }
};
