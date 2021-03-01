<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameInvoiceReceiptNumber extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('invoice_number', 'receipt_number');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
