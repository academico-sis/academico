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
        Schema::create('scheduled_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->bigInteger('value');
            $table->date('date');
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
