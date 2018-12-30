<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('enrollment_status_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('enrollment_status_types')->insert(
            array(
                'id' => 1,
                'name' => 'PENDIENTE'
            )
        );

        DB::table('enrollment_status_types')->insert(
            array(
                'id' => 2,
                'name' => 'PAGADA'
            )
        );

        DB::table('enrollment_status_types')->insert(
            array(
                'id' => 3,
                'name' => 'ANULADA'
            )
        );

        DB::table('enrollment_status_types')->insert(
            array(
                'id' => 4,
                'name' => 'TRASPASO'
            )
        );

        DB::table('enrollment_status_types')->insert(
            array(
                'id' => 5,
                'name' => 'DEVOLUCION'
            )
        );

        DB::table('enrollment_status_types')->insert(
            array(
                'id' => 6,
                'name' => 'NOTACREDITO'
            )
        );

        DB::table('enrollment_status_types')->insert(
            array(
                'id' => 7,
                'name' => 'SALDO'
            )
        );
        

        Schema::create('enrollments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('responsible_id')->unsigned();
            $table->integer('course_id')->unsigned();
            // invoice tables has one / many enrollments
            //$table->integer('invoice_id')->nullable()->unsigned(); // todo add FK constrain
            $table->integer('status_id')->unsigned()->default(1);
            //$table->text('comment')->nullable();
            // comments will be stored into the global comments table
            $table->integer('parent_id')->nullable()->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
}
