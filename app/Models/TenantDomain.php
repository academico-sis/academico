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
