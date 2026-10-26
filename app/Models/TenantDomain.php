<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

/**
 * A host name that leads to a tenant, such as a custom domain or a subdomain. Stored in the landlord database.
 */
class TenantDomain extends Model
{
    use UsesLandlordConnection;

    /**
     * Lower-case host names: labels of letters, digits and hyphens, separated by dots.
     */
    public const PATTERN = '/^([a-z0-9]([a-z0-9-]*[a-z0-9])?\.)+[a-z0-9]([a-z0-9-]*[a-z0-9])?$/';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function setDomainAttribute(string $value): void
    {
        $this->attributes['domain'] = Str::lower(trim($value));
    }
}
