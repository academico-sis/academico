<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('user_id')->unsigned(); // todo - check - or use polymorphism?
            $table->string('idnumber');
            $table->string('address');
            $table->integer('genre_id')->nullable();
            $table->date('birthdate');
            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
