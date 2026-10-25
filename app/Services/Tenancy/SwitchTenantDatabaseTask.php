<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantDatabaseTask as BaseSwitchTenantDatabaseTask;

/**
 * Points the default connection to the database of the current tenant. A single-tenant install keeps the
 * configured database, even when it was renamed after its tenant was registered; when no tenant is current,
 * the configured database is used.
 */
class SwitchTenantDatabaseTask extends BaseSwitchTenantDatabaseTask
{
    private ?string $configuredDatabase;

    public function __construct()
    {
        $this->configuredDatabase = config('database.connections.'.$this->tenantDatabaseConnectionName().'.database');
    }

    public function forgetCurrent(): void
    {
        $this->setTenantConnectionDatabaseName($this->configuredDatabase);
    }

    protected function setTenantConnectionDatabaseName(?string $databaseName): void
    {
        if (config('database.connections.'.$this->tenantDatabaseConnectionName().'.database') === $databaseName) {
            return;
        }

        parent::setTenantConnectionDatabaseName($databaseName);
    }

    public function makeCurrent(IsTenant $tenant): void
    {
        if (Tenant::usesSingleDatabase()) {
            return;
        }

        $this->setTenantConnectionDatabaseName($tenant->getDatabaseName());
    }
}
