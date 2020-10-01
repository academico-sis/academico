<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceIdColumnToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('invoice_id')->after('comment')->nullable();
            $table->unsignedBigInteger('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade')->after('pre_invoice_id')->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('enrollment_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
}
