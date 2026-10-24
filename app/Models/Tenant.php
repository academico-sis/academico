<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

/**
 * An institution, with its own database, files and domains. Stored in the landlord database.
 *
 * @property string $name
 * @property string $database
 * @property ?string $storage_folder Folder of its files under storage/, or null for the folders of a single-tenant install
 */
class Tenant extends BaseTenant
{
    protected $guarded = ['id'];

    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class);
    }

    /**
     * The address of the tenant, built from its primary domain and the scheme and port of APP_URL.
     */
    public function url(string $baseUrl): string
    {
        $domain = $this->domains->sortByDesc('is_primary')->first()?->domain;

        if (! $domain) {
            return $baseUrl;
        }

        $port = parse_url($baseUrl, PHP_URL_PORT);

        return (parse_url($baseUrl, PHP_URL_SCHEME) ?: 'https').'://'.$domain.($port ? ":{$port}" : '');
    }

    /**
     * True when the landlord tables live in the application database, which then holds a single tenant.
     */
    public static function usesSingleDatabase(): bool
    {
        return blank(config('multitenancy.landlord_database'));
    }

    /**
     * The tenant of a single-database install, or the configured default tenant of a multi-tenant one.
     * Null before the landlord tables are migrated.
     */
    public static function default(): ?self
    {
        try {
            if (static::usesSingleDatabase()) {
                return static::query()->orderBy('id')->first();
            }

            return filled(config('multitenancy.default_tenant')) ? static::find(config('multitenancy.default_tenant')) : null;
        } catch (QueryException) {
            return null;
        }
    }
}
