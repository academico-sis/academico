<?php

use App\Models\AttendanceType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_types', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
        });


        $attendance_type = new AttendanceType;
        $name = [
            'en' => 'PRESENT',
            'fr' => 'PRÉSENT(E)',
            'es' => 'PRESENTE'
         ];
        $attendance_type->setTranslations('name', $name);
        $attendance_type->save();



        $attendance_type = new AttendanceType;
        $name = [
            'en' => 'PARTIALLY PRESENT',
            'fr' => 'PRÉSENCE PARTIELLE',
            'es' => 'PRESENTE POR PARTE'
         ];
        $attendance_type->setTranslations('name', $name);
        $attendance_type->save();

        $attendance_type = new AttendanceType;
        $name = [
            'en' => 'PARTIALLY PRESENT',
            'fr' => 'PRÉSENCE PARTIELLE',
            'es' => 'PRESENTE POR PARTE'
         ];
        $attendance_type->setTranslations('name', $name);
        $attendance_type->save();

        $attendance_type = new AttendanceType;
        $name = [
            'en' => 'JUSTIFIED ABSENCE',
            'fr' => 'ABSENCE JUSTIFIED',
            'es' => 'AUSENCIA JUSTIFICADA'
         ];
        $attendance_type->setTranslations('name', $name);
        $attendance_type->save();

        $attendance_type = new AttendanceType;
        $name = [
            'en' => 'ABSENT',
            'fr' => 'ABSENT(E)',
            'es' => 'AUSENTE'
         ];
        $attendance_type->setTranslations('name', $name);
        $attendance_type->save();

        Schema::create('attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->integer('attendance_type_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('attendance_type_id')
            ->references('id')->on('attendance_types')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
