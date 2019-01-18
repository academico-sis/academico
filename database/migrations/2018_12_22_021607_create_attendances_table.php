<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('attendance_types')->insert(
            array(
                'id' => 1,
                'name' => 'PRÉSENT(E)'
            )
        );

        DB::table('attendance_types')->insert(
            array(
                'id' => 2,
                'name' => 'PRÉSENCE PARTIELLE'
            )
        );

        DB::table('attendance_types')->insert(
            array(
                'id' => 3,
                'name' => 'EXCUSÉ(E)'
            )
        );

        DB::table('attendance_types')->insert(
            array(
                'id' => 4,
                'name' => 'ABSENT(E)'
            )
        );

        Schema::create('attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->integer('attendance_type_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('attendance_type_id')
            ->references('id')->on('attendance_types')
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
        Schema::dropIfExists('attendances');
    }
}
