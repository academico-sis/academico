<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('course_evaluation_type');
        Schema::dropIfExists('course_skill');
        Schema::dropIfExists('course_grade_type');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
