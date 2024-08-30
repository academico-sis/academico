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
        Schema::table('fees', function (Blueprint $table) {
            $table->string('product_code')->after('price')->nullable();
        });

        Schema::table('books', function (Blueprint $table) {
            $table->string('product_code')->after('price')->nullable();
        });

        Schema::table('rhythms', function (Blueprint $table) {
            $table->string('product_code')->after('default_volume')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn('product_code');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('product_code');
        });

        Schema::table('rhythms', function (Blueprint $table) {
            $table->dropColumn('product_code');
        });
    }
};
