<?php

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

/**
 * Each institution is a tenant with its own database (see config/multitenancy.php). The package finds the
 * tenant of web requests at boot and runs the switch tasks.
 */
class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerLandlordConnection();
        $this->signUrlsPerTenant();
    }

    public function boot(): void
    {
        if (Tenant::usesSingleDatabase()) {
            // The landlord tables are migrated with the application tables.
            $this->loadMigrationsFrom(database_path('migrations/landlord'));

            // Commands, the scheduler and queue workers use the only tenant, so that queued jobs know it.
            if ($this->app->runningInConsole()) {
                $this->app->booted(fn () => Tenant::checkCurrent() || Tenant::default()?->makeCurrent());
            }
        }
    }

    /**
     * The landlord connection is the default connection with the landlord database, or the application
     * database itself for a single-tenant install.
     */
    private function registerLandlordConnection(): void
    {
        $name = config('multitenancy.landlord_database_connection_name');

        if (config("database.connections.{$name}") !== null) {
            return;
        }

        $default = config('database.connections.'.config('database.default'));

        // A database named in DB_URL would win over the landlord and tenant databases
        if (! Tenant::usesSingleDatabase() && filled($default['url'] ?? null)) {
            throw new RuntimeException('DB_URL cannot be used with LANDLORD_DB_DATABASE. Set DB_HOST, DB_PORT, DB_USERNAME and DB_PASSWORD instead.');
        }

        config(["database.connections.{$name}" => array_merge($default, array_filter([
            'database' => config('multitenancy.landlord_database'),
        ]))]);
    }

    /**
     * In a multi-tenant install, signed URLs (private files, password resets) are signed with a key of
     * the current tenant, so that a link of one tenant is not valid on the domain of another one.
     */
    private function signUrlsPerTenant(): void
    {
        $this->app->extend('url', function (UrlGenerator $url): UrlGenerator {
            return $url->setKeyResolver(function (): array {
                $keys = [config('app.key'), ...(config('app.previous_keys') ?? [])];
                $tenant = Tenant::current();

                if (Tenant::usesSingleDatabase() || ! $tenant) {
                    return $keys;
                }

                return array_map(fn (string $key): string => hash_hmac('sha256', "tenant:{$tenant->getKey()}", $key), $keys);
            });
        });
    }
}
