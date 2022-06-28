<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lead_types', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('lead_types', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
