<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperBook
 */
class Book extends Model
{
    use CrudTrait;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $appends = ['price_with_currency', 'type'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

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
        return 'book';
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
