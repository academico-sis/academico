<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->date('start');
            $table->date('end');
            $table->integer('year_id')->unsigned();
        });

        DB::table('periods')->insert([
            'name' => 'Period 1',
            'start' => date('Y-m-d', strtotime('-1 day')),
            'end' => date('Y-m-d', strtotime('+90 days')),
            'year_id' => 1,
        ]);

        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('period_id')
            ->references('id')->on('periods')
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('periods');
    }
}
