<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentPreInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollment_pre_invoice', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('enrollment_id')->unsigned();
            $table->integer('pre_invoice_id')->unsigned();
            //$table->timestamps();
        });

        Schema::table('enrollment_pre_invoice', function (Blueprint $table) {
            $table->foreign('enrollment_id')
            ->references('id')->on('enrollments')
            ->onDelete('cascade');
        });

        Schema::table('enrollment_pre_invoice', function (Blueprint $table) {
            $table->foreign('pre_invoice_id')
            ->references('id')->on('pre_invoices')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('enrollment_pre_invoice');
    }
}
