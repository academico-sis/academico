<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\URL;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

/**
 * Links in emails, queued notifications and files use the primary domain of the tenant. Web requests
 * already build their links from the request host; commands and queued jobs need the root URL.
 * A single-tenant install keeps APP_URL, which may have changed since its domain was registered.
 */
class SwitchTenantUrlTask implements SwitchTenantTask
{
    private string $configuredUrl;

    public function __construct()
    {
        $this->configuredUrl = (string) config('app.url');
    }

    public function makeCurrent(IsTenant $tenant): void
    {
        $this->useUrl($tenant instanceof Tenant && ! Tenant::usesSingleDatabase() ? $tenant->url($this->configuredUrl) : $this->configuredUrl);
    }

    public function forgetCurrent(): void
    {
        $this->useUrl($this->configuredUrl);
    }

    private function useUrl(string $url): void
    {
        config(['app.url' => $url]);

        if (app()->runningInConsole()) {
            URL::useOrigin($url);
            URL::forceScheme(parse_url($url, PHP_URL_SCHEME));
        }
    }
}
