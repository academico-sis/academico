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
        Schema::create('contact_relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->foreign('relationship_id')
            ->references('id')->on('contact_relationships')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('contact_relationships');
    }
};
