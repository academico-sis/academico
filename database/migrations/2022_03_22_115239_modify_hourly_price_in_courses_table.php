<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('courses', function (Blueprint $table) {
            $table->bigInteger('hourly_price')->nullable()->change();
        });

        DB::unprepared('update courses set hourly_price = 100*hourly_price');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('hourly_price', 8, 2)->nullable()->change();
        });

        DB::unprepared('update courses set hourly_price = hourly_price/100');
    }
};
