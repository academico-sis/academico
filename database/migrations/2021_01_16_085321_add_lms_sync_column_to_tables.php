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
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('sync_to_lms')->nullable();
            $table->bigInteger('lms_id')->nullable();
        });

        Schema::table('rhythms', function (Blueprint $table) {
            $table->bigInteger('lms_id')->nullable();
        });

        Schema::table('levels', function (Blueprint $table) {
            $table->bigInteger('lms_id')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('lms_id')->nullable();
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
