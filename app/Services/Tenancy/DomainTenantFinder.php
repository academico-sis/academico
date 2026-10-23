<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use App\Models\TenantDomain;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

/**
 * Finds the tenant from the request host. A single-tenant install answers on any host.
 */
class DomainTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?IsTenant
    {
        if (Tenant::usesSingleDatabase()) {
            return Tenant::default();
        }

        try {
            $domain = TenantDomain::query()->with('tenant')->where('domain', $request->getHost())->first();
        } catch (QueryException) {
            return null;
        }

        return $domain?->tenant ?? Tenant::default();
    }
}
