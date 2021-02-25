<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('pre_invoices', 'invoices');

        Schema::rename('pre_invoice_details', 'invoice_details');

        Schema::table('invoice_details', function (Blueprint $table) {
            $table->renameColumn('pre_invoice_id', 'invoice_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('pre_invoice_id', 'invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
