<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersSearchableLevels implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $value = collect($value)->toArray();
        $query->where(function (Builder $query) use ($value) {
            $query->whereIn('level_id', $value)->orWhereHas('children', function (Builder $query) use ($value) {
                $query->whereIn('level_id', $value);
            });
        });
    }
}
