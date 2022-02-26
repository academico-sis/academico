<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait PriceTrait
{
    public function price(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value / 100,
            set: fn ($value) => $value * 100,
        );
    }

    public function getPriceWithCurrencyAttribute(): string
    {
        if (config('academico.currency_position') === 'before') {
            return config('academico.currency_symbol').' '.$this->price;
        }

        return $this->price.' '.config('academico.currency_symbol');
    }
}
