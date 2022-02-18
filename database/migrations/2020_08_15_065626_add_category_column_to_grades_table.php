<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade_type_categories', function (Blueprint $table) {
            $table->id();
            $table->text('name');
        });

        DB::table('grade_type_categories')->insert(['id' => 1, 'name' => 'Evaluation continue']);

        Schema::table('grade_types', function (Blueprint $table) {
            $table->unsignedBigInteger('grade_type_category_id')->references('id')->on('grade_type_categories')->onDelete('restrict')->after('id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grade_type_categories');
    }
};
