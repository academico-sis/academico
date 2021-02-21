<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStylingColumnToResultTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('result_types', function (Blueprint $table) {
            $table->string('class')->nullable()->after('description');
            $table->string('icon')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('result_types', function (Blueprint $table) {
            $table->dropColumn(['class', 'icon']);
        });
    }
}
