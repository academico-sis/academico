<?php

namespace App\Services\Tenancy;

use Sentry\State\Scope;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

use function Sentry\configureScope;

/**
 * Error reports sent to Sentry carry the tenant ID. Log entries already do, through Laravel's context.
 */
class TagErrorReportsTask implements SwitchTenantTask
{
    public function makeCurrent(IsTenant $tenant): void
    {
        configureScope(fn (Scope $scope) => $scope->setTag('tenant', (string) $tenant->getKey()));
    }

    public function forgetCurrent(): void
    {
        configureScope(fn (Scope $scope) => $scope->removeTag('tenant'));
    }
}
