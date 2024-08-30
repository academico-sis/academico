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
        Schema::create('book_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable()->references('id')->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('book_id')->nullable()->references('id')->on('books')->onDelete('restrict');
            $table->unsignedBigInteger('status_id')->nullable()->references('id')->on('enrollment_status_types')->onDelete('restrict');
            $table->string('code')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_student');
    }
};
