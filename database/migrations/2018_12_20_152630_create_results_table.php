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
        Schema::create('results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('enrollment_id')->unique()->unsigned();
            $table->integer('result_type_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('result_types', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('results', function (Blueprint $table) {
            $table->foreign('result_type_id')
            ->references('id')->on('result_types')
            ->onDelete('restrict');

            $table->foreign('enrollment_id')
            ->references('id')->on('enrollments')
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('results');
        Schema::dropIfExists('result_types');
    }
};
