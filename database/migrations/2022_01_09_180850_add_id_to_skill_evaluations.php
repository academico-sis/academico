<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
            Schema::table('skill_evaluations', function (Blueprint $table) {
                $table->increments('id');
            });
        }
    }

    public function down()
    {
        Schema::table('skill_evaluations', function (Blueprint $table) {
            //
        });
    }
};
