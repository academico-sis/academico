<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
            $table->bigInteger('price')->nullable();
            //$table->timestamps();
        });

        Schema::create('book_course', function (Blueprint $table) {
            $table->integer('book_id')->unsigned();
            $table->integer('course_id')->unsigned();
            $table->index(['book_id', 'course_id']);
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_course');
    }
};
