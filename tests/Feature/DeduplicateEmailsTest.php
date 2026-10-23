<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class DeduplicateEmailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_reports_that_emails_are_unique(): void
    {
        $this->insertUser(1, 'jane@example.com');

        $this->artisan('academico:deduplicate-emails')
            ->expectsOutput('Every email address belongs to a single user.')
            ->assertSuccessful();
    }

    public function test_command_lists_the_users_sharing_an_email_without_changing_them(): void
    {
        $this->allowDuplicateEmails();
        $this->insertUser(1, 'jane@example.com');
        $this->insertUser(2, 'Jane@Example.com');

        $this->artisan('academico:deduplicate-emails')
            ->expectsOutputToContain('Email addresses shared by several users: 1.')
            ->assertFailed();

        $this->assertSame('Jane@Example.com', DB::table('users')->where('id', 2)->value('email'));
    }

    public function test_command_fix_keeps_the_address_on_the_oldest_account(): void
    {
        $this->allowDuplicateEmails();
        $this->insertUser(1, 'jane@example.com');
        $this->insertUser(2, 'Jane@Example.com');
        $this->insertUser(3, 'jane@example.com ');
        $this->insertUser(4, 'john@example.com');

        $this->artisan('academico:deduplicate-emails --fix')->assertSuccessful();

        $this->assertSame(
            [1 => 'jane@example.com', 2 => 'jane+2@example.com', 3 => 'jane+3@example.com', 4 => 'john@example.com'],
            DB::table('users')->orderBy('id')->pluck('email', 'id')->all(),
        );
    }

    public function test_migration_stops_when_users_share_an_email(): void
    {
        $this->allowDuplicateEmails();
        $this->insertUser(1, 'jane@example.com');
        $this->insertUser(2, 'JANE@example.com');

        $migration = require database_path('migrations/2026_10_23_182000_make_user_emails_unique.php');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('academico:deduplicate-emails');

        $migration->up();
    }

    /**
     * Installs that predate the unique index can hold duplicates.
     */
    private function allowDuplicateEmails(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }

    private function insertUser(int $id, string $email): void
    {
        DB::table('users')->insert([
            'id' => $id,
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'email' => $email,
            'password' => 'secret',
        ]);
    }
}
