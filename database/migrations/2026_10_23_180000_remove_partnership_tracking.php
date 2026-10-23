<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn(['started_on', 'expired_on', 'auto_renewal', 'last_alert_sent_at']);
        });

        DB::table('settings')->where('key', 'settings.partnership_alerts')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->date('started_on')->nullable();
            $table->date('expired_on')->nullable();
            $table->boolean('auto_renewal')->nullable();
            $table->timestamp('last_alert_sent_at')->nullable();
        });
    }
};
