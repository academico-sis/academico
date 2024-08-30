<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_invoices', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('pre_invoice_details', function (Blueprint $table) {
            $table->string('product_code')->after('product_name')->nullable();
            $table->integer('product_id')->after('product_code')->nullable();
            $table->string('product_type')->after('product_id')->nullable();
        });

        Schema::dropIfExists('carts');
        Schema::dropIfExists('cart_product');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
