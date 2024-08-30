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
        Schema::table('result_types', function (Blueprint $table) {
            $table->string('class')->nullable()->after('description');
            $table->string('icon')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('result_types', function (Blueprint $table) {
            $table->dropColumn(['class', 'icon']);
        });
    }
};
