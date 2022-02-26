<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete()->restrictOnUpdate();
        });

        Schema::table('invoice_details', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete()->restrictOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
};
