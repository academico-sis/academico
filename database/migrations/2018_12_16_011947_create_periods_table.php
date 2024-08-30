<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->date('start');
            $table->date('end');
            $table->integer('year_id')->unsigned();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('period_id')
            ->references('id')->on('periods')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('periods');
    }
};
