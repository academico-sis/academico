<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('locale')->default(config('app.locale'));
        });

        Schema::table('contacts', function ($table) {
            $table->string('locale')->default(config('app.locale'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('locale');
        });

        Schema::table('contacts', function ($table) {
            $table->dropColumn('locale');
        });
    }
};
