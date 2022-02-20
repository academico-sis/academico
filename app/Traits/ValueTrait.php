<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait ValueTrait
{
    public function getValueWithCurrencyAttribute()
    {
        if (config('academico.currency_position') === 'before') {
            return config('academico.currency_symbol').' '.$this->value;
        }

        return $this->value.' '.config('academico.currency_symbol');
    }

    public function value(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value / 100,
            set: fn ($value) => $value * 100,
        );
    }
}
