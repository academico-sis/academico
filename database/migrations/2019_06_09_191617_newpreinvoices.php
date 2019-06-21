<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Newpreinvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();

        Schema::table('pre_invoices', function (Blueprint $table) {
            $table->integer('enrollment_id');
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
        Schema::table('pre_invoices', function (Blueprint $table) {
            $table->dropColumn('enrollment_id');
        });
    }
}
