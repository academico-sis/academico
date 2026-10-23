<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

/**
 * Runs the migrations after an upgrade: the landlord database first, then the database of each institution.
 * A single-tenant install has one database, migrated as usual.
 */
class MigrateTenants extends Command
{
    protected $signature = 'academico:migrate {--force : Run the migrations in production without asking}';

    protected $description = 'Run the migrations of the landlord database and of every institution';

    public function handle(): int
    {
        if (Tenant::usesSingleDatabase()) {
            return $this->call('migrate', ['--force' => $this->option('force')]);
        }

        $this->info('Landlord database');
        $status = $this->call('migrate', [
            '--database' => config('multitenancy.landlord_database_connection_name'),
            '--path' => database_path('migrations/landlord'),
            '--realpath' => true,
            '--force' => $this->option('force'),
        ]);

        foreach (Tenant::query()->orderBy('id')->get() as $tenant) {
            $this->info("{$tenant->name} ({$tenant->database})");
            $status = max($status, $tenant->execute(fn (): int => $this->call('migrate', [
                '--path' => database_path('migrations'),
                '--realpath' => true,
                '--force' => $this->option('force'),
            ])));
        }

        return $status;
    }
}
