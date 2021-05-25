<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchedulingToPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->date('date')->nullable()->after('payment_method');
            $table->unsignedInteger('status')->nullable()->after('value');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
}
