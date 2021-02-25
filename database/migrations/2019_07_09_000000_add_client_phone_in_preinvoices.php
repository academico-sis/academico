<?php

use Illuminate\Database\Migrations\Migration;

class AddClientPhoneInPreinvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_invoices', function ($table) {
            $table->string('client_phone')->after('client_email')->nullable();
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
