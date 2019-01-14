<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('price', 8, 2);
            //$table->timestamps();
        });

        Schema::create('book_course', function (Blueprint $table) {
            $table->integer('book_id')->unsigned();
            $table->integer('course_id')->unsigned();
        });

        Schema::table('book_course', function (Blueprint $table) {
            $table->foreign('book_id')
            ->references('id')->on('books')
            ->onDelete('restrict');
        });

        Schema::table('book_course', function (Blueprint $table) {
            $table->foreign('course_id')
            ->references('id')->on('courses')
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
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_course');
    }
}
