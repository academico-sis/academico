<?php

namespace App\Models;

use App\Models\Interfaces\InvoiceableModel;
use App\Traits\PriceTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model implements InvoiceableModel
{
    use CrudTrait;
    use PriceTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $appends = ['price_with_currency', 'type'];

    public function getTypeAttribute(): string
    {
        return 'fee';
    }
}
