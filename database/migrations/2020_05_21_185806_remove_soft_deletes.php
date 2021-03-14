<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RemoveSoftDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('contacts', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('events', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('payments', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('students', function ($table) {
            $table->dropColumn('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
