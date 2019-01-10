<?php

use App\Models\ResultType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('enrollment_id')->unique()->unsigned();
            $table->integer('result_type_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('result_types', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name'); // fix JSON not working
            $table->text('description');
            $table->timestamps();
        });

        $result_type = new ResultType;

        $name = [
            'en' => 'PASS',
            'fr' => 'VALIDÉ',
            'es' => 'APROBADO'
         ];

         $description = [
            'en' => 'Allowed to enter next level',
            'fr' => 'Peut passer au niveau suivant',
            'es' => 'Puede entrar al nivel siguiente'
         ];

        $result_type->setTranslations('name', $name);
        $result_type->setTranslations('description', $description);
        $result_type->save();


        $result_type = new ResultType;

        $name = [
            'en' => 'FAIL',
            'fr' => 'NON VALIDÉ',
            'es' => 'REPROBADO'
         ];

         $description = [
            'en' => 'May not enter next level',
            'fr' => 'Ne peut pas passer au niveau suivant',
            'es' => 'No puede entrar al nivel siguiente'
         ];

        $result_type->setTranslations('name', $name);
        $result_type->setTranslations('description', $description);
        $result_type->save();



        $result_type = new ResultType;

        $name = [
            'en' => 'SEE COORD.',
            'fr' => 'VOIR DIR.',
            'es' => 'VER COORD.'
         ];

         $description = [
            'en' => 'Consult with Academic affairs',
            'fr' => 'Voir avec la Coordination',
            'es' => 'Ver con el departamento pedagogico'
         ];

        $result_type->setTranslations('name', $name);
        $result_type->setTranslations('description', $description);
        $result_type->save();

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
        Schema::dropIfExists('result_types');

    }
}
