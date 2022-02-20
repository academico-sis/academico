<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Eloquent\Casts\Attribute;

interface InvoiceableModel
{
    public function getTypeAttribute(): string;
    function price(): Attribute;
    public function getPriceWithCurrencyAttribute(): string;
}
