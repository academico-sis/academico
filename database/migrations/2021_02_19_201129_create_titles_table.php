<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('titles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('titles');
    }
};
