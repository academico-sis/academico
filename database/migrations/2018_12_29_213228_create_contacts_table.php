<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('idnumber')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable(); // if null; look in the users table
            $table->integer('relationship_id')->nullable()->unsigned();
            $table->timestamps();
            //$table->softDeletes();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->foreign('student_id')
            ->references('id')->on('students')
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
        Schema::dropIfExists('contacts');
    }
}
