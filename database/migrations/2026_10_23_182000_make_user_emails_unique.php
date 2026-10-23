<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Users log in with their email address, which becomes unique. Usernames are no longer used.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')->whereRaw("TRIM(email) = ''")->update(['email' => null]);

        $duplicates = DB::table('users')
            ->whereNotNull('email')
            ->selectRaw('LOWER(TRIM(email)) as normalized_email')
            ->groupByRaw('LOWER(TRIM(email))')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isNotEmpty()) {
            throw new RuntimeException('Email addresses must now be unique, but some are shared by several users. Run `php artisan academico:deduplicate-emails` to list them, change them or run the command with --fix, then run the migrations again.');
        }

        DB::table('users')->whereNotNull('email')->update(['email' => DB::raw('LOWER(TRIM(email))')]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->unique('email');
        });

        DB::table('settings')->where('key', 'registration.ensure_email_unicity')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->string('username')->nullable()->after('id');
        });

        foreach (DB::table('users')->get(['id', 'email']) as $user) {
            DB::table('users')->where('id', $user->id)->update(['username' => $user->email ?? 'user'.$user->id]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });
    }
};
