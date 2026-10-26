<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Teachers were reminded of their classes without attendance when the absence emails to students were on.
 * The reminders now have their own setting, which starts with the value of the absence emails.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $absenceEmails = DB::table('settings')->where('key', 'academico.send_emails_for_absences')->value('value');

        if ($absenceEmails !== null && json_decode($absenceEmails) === true) {
            DB::table('settings')->insertOrIgnore([
                'key' => 'academico.send_attendance_reminders',
                'value' => json_encode(true),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'academico.send_attendance_reminders')->delete();
    }
};
