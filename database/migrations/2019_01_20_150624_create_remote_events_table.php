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
        Schema::create('remote_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->unsigned();
            $table->string('name');
            $table->bigInteger('worked_hours')->unsigned();
            $table->integer('period_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('remote_events', function (Blueprint $table) {
            $table->foreign('period_id')
            ->references('id')->on('periods')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('remote_events');
    }
};
