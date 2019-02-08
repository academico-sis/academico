<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactRelationships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('contact_relationships');
    }
}
