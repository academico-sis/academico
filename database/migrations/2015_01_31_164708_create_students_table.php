<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('id')->unsigned()->unique();
            $table->string('idnumber')->nullable();
            $table->string('address')->nullable();
            $table->integer('gender_id')->nullable();
            $table->date('birthdate')->nullable();
            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamps();
            //$table->softDeletes();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreign('id')
            ->references('id')->on('users')
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

        Schema::dropIfExists('students');
    }
}
