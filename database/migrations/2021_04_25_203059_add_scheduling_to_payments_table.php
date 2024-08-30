<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->date('date')->nullable()->after('payment_method');
            $table->unsignedInteger('status')->nullable()->after('value');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
};
