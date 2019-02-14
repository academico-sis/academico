<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('students', function ($table) {
            $table->unsignedInteger('lead_type_id')->nullable();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreign('lead_type_id')
            ->references('id')->on('lead_types')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_types');

        Schema::table('students', function ($table) {
            $table->dropColumn('lead_type_id');
        });
    }
}
