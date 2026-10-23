<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Values of the former config table that are still in use, with their setting key.
     *
     * @var array<string, string>
     */
    private array $keptConfigKeys = [
        'current_period' => 'current_period',
        'default_enrollment_period' => 'default_enrollment_period',
        'first_period' => 'first_period',
        'first_external_period' => 'first_external_period',
        'invoice_footer' => 'invoicing.invoice_footer',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        if (! Schema::hasTable('config')) {
            return;
        }

        $rows = DB::table('config')->whereIn('name', array_keys($this->keptConfigKeys))->get();

        foreach ($rows as $row) {
            $value = is_numeric($row->value) && $row->name !== 'invoice_footer' ? (int) $row->value : $row->value;

            DB::table('settings')->insert([
                'key' => $this->keptConfigKeys[$row->name],
                'value' => json_encode($value),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::drop('config');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        $configNames = array_flip($this->keptConfigKeys);

        foreach (DB::table('settings')->whereIn('key', array_keys($configNames))->get() as $row) {
            DB::table('config')->insert([
                'name' => $configNames[$row->key],
                'value' => json_decode($row->value),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::dropIfExists('settings');
    }
};
