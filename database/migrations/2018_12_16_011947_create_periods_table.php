<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            'start' => date('Y-m-d', strtotime('first day of january this year')),
            'end' => date('Y-m-d', strtotime('last day of march this year')),
            'year_id' => 1
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
