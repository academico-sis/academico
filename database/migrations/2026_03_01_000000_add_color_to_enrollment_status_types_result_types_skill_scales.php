<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_status_types', function (Blueprint $table) {
            $table->string('color')->nullable();
        });

        Schema::table('result_types', function (Blueprint $table) {
            $table->string('color')->nullable();
        });

        Schema::table('skill_scales', function (Blueprint $table) {
            $table->string('color')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_status_types', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('result_types', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('skill_scales', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
