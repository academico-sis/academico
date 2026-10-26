<?php

namespace App\Services\Tenancy;

use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use Spatie\Permission\PermissionRegistrar;

/**
 * Roles and permissions are kept in memory and in the cache: load them again from the cache store,
 * which PrefixCacheTask has just given the tenant prefix.
 */
class SwitchTenantPermissionCacheTask implements SwitchTenantTask
{
    public function makeCurrent(IsTenant $tenant): void
    {
        app(PermissionRegistrar::class)->initializeCache();
    }

    public function forgetCurrent(): void
    {
        app(PermissionRegistrar::class)->initializeCache();
    }
}
