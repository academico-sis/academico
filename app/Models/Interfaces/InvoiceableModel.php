<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Eloquent\Casts\Attribute;

interface InvoiceableModel
{
    public function getTypeAttribute(): string;

    public function price(): Attribute;

    public function getPriceWithCurrencyAttribute(): string;
}
