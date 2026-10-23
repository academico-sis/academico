<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Email addresses are unique. Installs where several users share an address must resolve these duplicates
 * before the migration that adds the unique index can run.
 */
class DeduplicateEmails extends Command
{
    protected $signature = 'academico:deduplicate-emails {--fix : Keep each address on its oldest account and give the other accounts a plus address}';

    protected $description = 'List the email addresses shared by several users. With --fix, keep each address on its oldest account and change the others to name+ID@domain.';

    public function handle(): int
    {
        $duplicates = $this->duplicates();

        if ($duplicates->isEmpty()) {
            $this->info('Every email address belongs to a single user.');

            return Command::SUCCESS;
        }

        $this->table(
            ['Email', 'User ID', 'Name', 'Created', 'Deleted'],
            $duplicates->flatten(1)->map(fn (object $user): array => [
                $user->email, $user->id, trim($user->firstname.' '.$user->lastname), $user->created_at, $user->deleted_at,
            ])->all(),
        );

        if (! $this->option('fix')) {
            $this->warn("Email addresses shared by several users: {$duplicates->count()}. Change them in the student or teacher forms, or run this command again with --fix.");

            return Command::FAILURE;
        }

        foreach ($duplicates as $users) {
            foreach ($users->slice(1) as $user) {
                $email = $this->plusAddress($user->email, $user->id);
                DB::table('users')->where('id', $user->id)->update(['email' => $email]);

                $this->line("User {$user->id}: {$user->email} → {$email}");
            }
        }

        $this->info('Every email address now belongs to a single user.');

        return Command::SUCCESS;
    }

    /**
     * Users sharing an email address (ignoring case and spaces), oldest first, including deleted users.
     *
     * @return Collection<string, Collection<int, object>>
     */
    private function duplicates(): Collection
    {
        return DB::table('users')
            ->whereNotNull('email')
            ->orderBy('id')
            ->get(['id', 'firstname', 'lastname', 'email', 'created_at', 'deleted_at'])
            ->filter(fn (object $user): bool => trim($user->email) !== '')
            ->groupBy(fn (object $user): string => Str::lower(trim($user->email)))
            ->filter(fn (Collection $users): bool => $users->count() > 1);
    }

    private function plusAddress(string $email, int $userId): string
    {
        $email = Str::lower(trim($email));

        if (! str_contains($email, '@')) {
            return "{$email}+{$userId}";
        }

        return Str::beforeLast($email, '@')."+{$userId}@".Str::afterLast($email, '@');
    }
}
