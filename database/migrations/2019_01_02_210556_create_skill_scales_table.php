<?php

use App\Models\SkillScale;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillScalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_scales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shortname');
            $table->string('name')->nullable();
            $table->decimal('value', 2, 1); // decimal number between 0 and 1
            $table->timestamps();
            $table->softDeletes(); 
        });


        $skill_scale = new SkillScale;

        $shortname = [
            'en' => 'NO',
            'fr' => 'NON',
            'es' => 'NO'
         ];

        $name = [
            'en' => 'Non aquired',
            'fr' => 'Non acquis',
            'es' => 'No adquirido'
         ];

        $skill_scale->setTranslations('name', $shortname);
        $skill_scale->setTranslations('name', $name);
        $skill_scale->value = 0;
        $skill_scale->save();

        $skill_scale = new SkillScale;
        $shortname = [
            'en' => 'PR',
            'fr' => 'EC',
            'es' => 'EC'
         ];

        $name = [
            'en' => 'In progress',
            'fr' => 'En cours d\'aquisition',
            'es' => 'En curso de adquisicion'
         ];

        $skill_scale->setTranslations('name', $shortname); 
        $skill_scale->setTranslations('name', $name);
        $skill_scale->value = 0.5;
        $skill_scale->save();


        $skill_scale = new SkillScale;
        $shortname = [
            'en' => 'YES',
            'fr' => 'OUI',
            'es' => 'SI'
         ];

        $name = [
            'en' => 'Aquired',
            'fr' => 'Acquis',
            'es' => 'Adquirido'
         ];
         $skill_scale->setTranslations('name', $shortname); 

        $skill_scale->setTranslations('name', $name);
        $skill_scale->value = 1;
        $skill_scale->save();


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_scales');
    }
}
