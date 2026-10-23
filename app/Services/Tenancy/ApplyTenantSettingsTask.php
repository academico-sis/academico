<?php

namespace App\Services\Tenancy;

use App\Services\Settings;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

/**
 * Applies the institution settings stored in the tenant database, and its language as default locale.
 * Web requests then switch to the language of the user (see SetLocale).
 */
class ApplyTenantSettingsTask implements SwitchTenantTask
{
    public function makeCurrent(IsTenant $tenant): void
    {
        app(Settings::class)->reload();
        app()->setLocale(config('academico.locale'));
    }

    public function forgetCurrent(): void
    {
        app(Settings::class)->forget();
    }
}
