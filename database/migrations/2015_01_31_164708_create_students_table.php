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
            $table->increments('id');
            $table->integer('user_id')->unsigned()->unique();
            $table->string('idnumber')->unique();
            $table->string('address');
            $table->integer('genre_id')->nullable();
            $table->date('birthdate');
            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamps();
            //$table->softDeletes();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreign('user_id')
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
