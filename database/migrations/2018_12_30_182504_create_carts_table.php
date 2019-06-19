<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * This table stores the pending carts for each user
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('cart_product', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cart_id')->unsigned();
            $table->integer('product_id');
            $table->string('product_type');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('student_id')
            ->references('id')->on('students')
            ->onDelete('cascade');
        });

        Schema::table('cart_product', function (Blueprint $table) {
            $table->foreign('cart_id')
            ->references('id')->on('carts')
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

        Schema::dropIfExists('carts');
        Schema::dropIfExists('cart_product');
    }
}
