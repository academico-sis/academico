<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('responsable_id')->unsigned();
            $table->integer('pre_invoice_id')->unsigned();
            $table->string('payment_method');
            $table->decimal('value', 8, 2);
            $table->string('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('responsable_id')
            ->references('id')->on('users')
            ->onDelete('restrict');

            $table->foreign('pre_invoice_id')
            ->references('id')->on('pre_invoices')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
