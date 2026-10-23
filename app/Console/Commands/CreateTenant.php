<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use App\Services\Settings;
use Database\Seeders\InstitutionSeeder;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Creates an institution in a multi-tenant install: its database, with the tables, reference data and a first
 * period, its files folder, its domain, and a first administrator who receives a link to choose a password.
 */
class CreateTenant extends Command
{
    protected $signature = 'academico:tenant:create
        {name : Name of the institution}
        {domain : Main host name, such as school.example.org}
        {--database= : Name of the new database (default: academico_ followed by the name)}
        {--admin-email= : Email address of the first administrator}
        {--admin-firstname= : First name of the first administrator}
        {--admin-lastname= : Last name of the first administrator}';

    protected $description = 'Create an institution with its database, files folder, domain and first administrator';

    public function handle(): int
    {
        if (Tenant::usesSingleDatabase()) {
            $this->error('This install serves a single institution. Set LANDLORD_DB_DATABASE to serve several.');

            return Command::FAILURE;
        }

        $data = [
            'name' => trim((string) $this->argument('name')),
            'domain' => Str::lower(trim((string) $this->argument('domain'))),
            'database' => $this->option('database') ?: 'academico_'.Str::limit(Str::slug((string) $this->argument('name'), '_'), 54, ''),
            'email' => $this->option('admin-email') ?? $this->ask('Email address of the first administrator'),
            'firstname' => $this->option('admin-firstname') ?? $this->ask('First name of the first administrator'),
            'lastname' => $this->option('admin-lastname') ?? $this->ask('Last name of the first administrator'),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'max:255'],
            'domain' => ['required', 'max:253', 'regex:/^([a-z0-9]([a-z0-9-]*[a-z0-9])?\.)+[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', Rule::unique(TenantDomain::class, 'domain')],
            // A SQLite database is a file path; database servers limit names to 64 letters, digits and underscores
            'database' => ['required', Rule::unique(Tenant::class, 'database'), ...($this->landlordDriver() === 'sqlite' ? ['max:255'] : ['max:64', 'regex:/^[A-Za-z0-9_]+$/'])],
            'email' => ['required', 'email', 'max:255'],
            'firstname' => ['required', 'max:255'],
            'lastname' => ['required', 'max:255'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return Command::FAILURE;
        }

        Schema::connection($this->landlordConnection())->createDatabase($data['database']);

        $tenant = Tenant::create(['name' => $data['name'], 'database' => $data['database']]);
        $tenant->update(['storage_folder' => "tenants/{$tenant->id}"]);
        $tenant->domains()->create(['domain' => $data['domain'], 'is_primary' => true]);

        try {
            $resetLink = $tenant->execute(fn (): string => $this->prepareDatabase($data));
        } catch (Throwable $exception) {
            // Remove what was created, so that the command can be run again
            $tenant->delete();
            Schema::connection($this->landlordConnection())->dropDatabaseIfExists($data['database']);

            throw $exception;
        }

        $this->table(['ID', 'Name', 'Database', 'Domain', 'Files'], [
            [$tenant->id, $tenant->name, $tenant->database, $data['domain'], storage_path($tenant->storage_folder)],
        ]);
        $this->info("The administrator {$data['email']} has been sent a link to choose a password:");
        $this->line($resetLink);
        $this->line('If the link has expired, use "Forgot password?" on the login page.');

        return Command::SUCCESS;
    }

    /**
     * Runs with the new tenant current. Returns the password reset link of the administrator.
     *
     * @param  array<string, string>  $data
     */
    private function prepareDatabase(array $data): string
    {
        $this->call('migrate', ['--path' => database_path('migrations'), '--realpath' => true, '--force' => true]);
        $this->call('db:seed', ['--class' => InstitutionSeeder::class, '--force' => true]);

        app(Settings::class)->set(['academico.company_name' => $data['name']]);

        $admin = User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(40)),
        ]);
        $admin->assignRole('admin');

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $token = Password::broker(Filament::getAuthPasswordBroker())->createToken($admin);
        $notification = new ResetPassword($token);
        $notification->url = Filament::getResetPasswordUrl($token, $admin);

        try {
            $admin->notify($notification);
        } catch (Throwable $exception) {
            report($exception);
            $this->warn("The email could not be sent: {$exception->getMessage()}");
        }

        return $notification->url;
    }

    private function landlordConnection(): string
    {
        return config('multitenancy.landlord_database_connection_name');
    }

    private function landlordDriver(): ?string
    {
        return config('database.connections.'.$this->landlordConnection().'.driver');
    }
}
