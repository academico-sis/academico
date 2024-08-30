<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('invoice_types')) {
            Schema::create('invoice_types', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->bigInteger('invoice_type_id')->nullable()->after('id');
                $table->bigInteger('invoice_number')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_types');
    }
};
