<?php

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Support\ServiceProvider;

/**
 * Each institution is a tenant with its own database (see config/multitenancy.php). The package finds the
 * tenant of web requests at boot and runs the switch tasks.
 */
class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerLandlordConnection();
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

        config(["database.connections.{$name}" => array_merge($default, array_filter([
            'database' => config('multitenancy.landlord_database'),
        ]))]);
    }
}
