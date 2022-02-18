<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('enrollment_invoice');

        if (Schema::hasColumn('payments', 'receipt_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('receipt_id');
            });
        }

        if (Schema::hasColumn('payments', 'status')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
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
};
