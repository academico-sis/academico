<?php

use App\Models\EvaluationType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_types', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            //$table->timestamps();
        });

        $eval_type = new EvaluationType;
        $name = [
            'en' => 'Grades',
            'fr' => 'Notes',
            'es' => 'Notas'
         ];
        $eval_type->setTranslations('name', $name);
        $eval_type->save();

        $eval_type = new EvaluationType;
        $name = [
            'en' => 'Skills',
            'fr' => 'Compétences',
            'es' => 'Competencias'
         ];
        $eval_type->setTranslations('name', $name);
        $eval_type->save();

        Schema::create('course_evaluation_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->integer('evaluation_type_id')->unsigned();
            //$table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_types');
    }
}
