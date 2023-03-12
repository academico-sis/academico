<?php

use App\Models\AttendanceType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_types', function (Blueprint $table) {
            $table->string('color')->nullable();
        });

        foreach (AttendanceType::all() as $type) {
            if ($type->id === 1) {
                $type->update(['color' => '#00C851']);
            }

            if ($type->id === 2) {
                $type->update(['color' => '#FFBB33']);
            }

            if ($type->id === 3) {
                $type->update(['color' => '#33B5E5']);
            }

            if ($type->id === 4) {
                $type->update(['color' => '#ff4444']);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_types', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
