<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Turns a test into a multi-tenant install, with SQLite files as tenant databases.
 */
trait InteractsWithTenants
{
    /**
     * Switching tenants replaces the default connection: the test transaction stays on the test database.
     *
     * @var list<string>
     */
    protected array $connectionsToTransact = ['sqlite'];

    private string $tenantDatabasesFolder;

    /**
     * The landlord tables move to their own in-memory database, and tenants are switched on a separate
     * default connection, which leaves the test database alone.
     */
    protected function useSeveralTenants(): void
    {
        Tenant::forgetCurrent();

        $this->tenantDatabasesFolder = storage_path('framework/testing/tenants-'.Str::random(8));
        File::ensureDirectoryExists($this->tenantDatabasesFolder);
        $this->beforeApplicationDestroyed(fn () => File::deleteDirectory($this->tenantDatabasesFolder));

        config([
            'multitenancy.landlord_database' => 'landlord',
            'multitenancy.landlord_database_connection_name' => 'landlord_test',
            'database.connections.landlord_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
            'database.connections.tenant_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
            'database.default' => 'tenant_test',
        ]);

        Artisan::call('migrate', ['--database' => 'landlord_test', '--path' => database_path('migrations/landlord'), '--realpath' => true]);
    }

    protected function tenantDatabasePath(string $name): string
    {
        return "{$this->tenantDatabasesFolder}/{$name}.sqlite";
    }

    /**
     * A tenant whose database only holds a settings table.
     *
     * @param  array<string, mixed>  $settings
     */
    protected function tenantWithSettings(string $name, array $settings = []): Tenant
    {
        $database = $this->tenantDatabasePath($name);
        File::put($database, '');
        config(["database.connections.{$name}" => ['driver' => 'sqlite', 'database' => $database, 'prefix' => '']]);

        Schema::connection($name)->create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        foreach ($settings as $key => $value) {
            DB::connection($name)->table('settings')->insert(['key' => $key, 'value' => json_encode($value)]);
        }

        return Tenant::create(['name' => $name, 'database' => $database]);
    }
}
