<?php

use App\Models\CachedReport;
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
        Schema::create(CachedReport::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->integer('year_id')->nullable();
            $table->integer('period_id')->nullable();
            $table->string('period_name');
            $table->integer('students');
            $table->integer('enrollments');
            $table->decimal('acquisition_rate')->nullable();
            $table->integer('new_students')->nullable();
            $table->decimal('taught_hours');
            $table->decimal('sold_hours');
            $table->decimal('takings')->nullable();
            $table->decimal('avg_takings')->nullable();
            $table->timestamp('updated_at');
            $table->integer('order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(CachedReport::TABLE_NAME);
    }
};
