<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grade_type_id')->unsigned();
            $table->integer('enrollment_id')->unsigned();
            $table->decimal('grade', 4, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->foreign('enrollment_id')
            ->references('id')->on('enrollments')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('grades');
    }
};
