<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperFee
 */
class Fee extends Model
{
    use CrudTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $appends = ['price_with_currency', 'type'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    public function getPriceAttribute($value)
    {
        return $value / 100;
    }

    public function getPriceWithCurrencyAttribute()
    {
        if (config('academico.currency_position') === 'before') {
            return config('academico.currency_symbol').' '.$this->price;
        }

        return $this->price.' '.config('academico.currency_symbol');
    }

    public function getTypeAttribute()
    {
        return 'fee';
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }
}
