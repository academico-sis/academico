<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('invoices', 'invoice_number')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->renameColumn('invoice_number', 'receipt_number');
            });
        }
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
};
