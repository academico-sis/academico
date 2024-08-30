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
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('commentable_id')->unsigned();
            $table->string('commentable_type');
            $table->text('body');
            $table->boolean('action')->default(false)->nullable();
            $table->integer('author_id')->unsigned()->nullable();
            $table->timestamps();
            //$table->softDeletes();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('author_id')
            ->references('id')->on('users')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('comments');
    }
};
