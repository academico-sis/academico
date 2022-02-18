<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('zip_code')->after('address')->nullable();
            $table->string('iban')->after('institution_id')->nullable();
            $table->string('bic')->after('iban')->nullable();
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            //
        });
    }
};
