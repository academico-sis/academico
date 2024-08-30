<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('enrollment_invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('scheduled_payment_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_invoice', function (Blueprint $table) {
            //
        });
    }
};
