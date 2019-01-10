<?php

use App\Models\UserDataRelationship;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDataRelationships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_data_relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            //$table->timestamps();
        });

        $attendance_type = new UserDataRelationship;
        $name = [
            'en' => 'FAMILY',
            'fr' => 'FAMILLE',
            'es' => 'FAMILIA'
         ];
        $attendance_type->setTranslations('name', $name);
        $attendance_type->save();


        $attendance_type = new UserDataRelationship;
        $name = [
            'en' => 'WORK',
            'fr' => 'TRAVAIL',
            'es' => 'TRABAJO'
         ];
        $attendance_type->setTranslations('name', $name);
        $attendance_type->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_data_relationships');
    }
}
