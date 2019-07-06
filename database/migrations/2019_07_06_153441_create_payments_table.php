<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('responsable_id');
            $table->integer('invoice_id');
            $table->string('payment_method');
            $table->decimal('value', 8, 2);
            $table->string('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('pre_invoice_details', function (Blueprint $table) {
            $table->softDeletes();
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
