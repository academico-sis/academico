<?php

namespace App\Models\Builders;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;

/**
 * Commands find tenants by ID or name, with --tenant=2 or --tenant="2 Rivers School". MariaDB compares a
 * string with the integer id by casting it, so "2 Rivers School" would also match the tenant with ID 2:
 * values that are not whole numbers are left out of the conditions on the ID.
 *
 * @extends Builder<Tenant>
 */
class TenantBuilder extends Builder
{
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        if ($this->isKeyColumn($column)) {
            $values = collect($values)->filter(fn ($value): bool => is_int($value) || ctype_digit((string) $value))->values()->all();
        }

        $this->query->whereIn($column, $values, $boolean, $not);

        return $this;
    }

    public function orWhereIn($column, $values)
    {
        return $this->whereIn($column, $values, 'or');
    }

    private function isKeyColumn(mixed $column): bool
    {
        return in_array($column, [$this->model->getKeyName(), $this->model->getQualifiedKeyName()], true);
    }
}
