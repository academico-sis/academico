<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Fee
 *
 * @property int $id
 * @property string $name
 * @property string $price
 * @property string|null $product_code
 * @property int $default
 * @property-read mixed $price_with_currency
 * @property-read mixed $type
 * @method static \Illuminate\Database\Eloquent\Builder|Fee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereProductCode($value)
 * @mixin \Eloquent
 */
class Fee extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    // protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $appends = ['price_with_currency', 'type'];
    //protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

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
        if (config('app.currency_position') === 'before')
        {
            return config('app.currency_symbol') . " ". $this->price;
        }

        return $this->price . " " . config('app.currency_symbol');
    }

    public function getTypeAttribute()
    {
        return "fee";
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
