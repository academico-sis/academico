<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        DB::table('config')->insert([
            ['name' => 'default_enrollment_period', 'value' => null],
            ['name' => 'current_period', 'value' => null],
            ['name' => 'institution_rules_url', 'value' => null],
            ['name' => 'moodle_url', 'value' => null],
            ['name' => 'moodle_token', 'value' => null],
            ['name' => 'first_period', 'value' => 1],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config');
    }
}
