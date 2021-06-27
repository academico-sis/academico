<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class InvoiceDetail extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;
    protected $appends = ['price_with_currency'];


    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->morphTo();
    }

    public function getPriceAttribute($value)
    {
        return ($value * $this->quantity )/ 100;
    }

    public function getPriceWithCurrencyAttribute()
    {
        if (config('app.currency_position') === 'before')
        {
            return config('app.currency_symbol') . " ". $this->price;
        }

        return $this->price . " " . config('app.currency_symbol');
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
