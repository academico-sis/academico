<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Payment extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $appends = ['date_for_humans', 'value_with_currency'];
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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

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
    public function getDateForHumansAttribute()
    {
        return Carbon::parse($this->created_at, 'UTC')->locale(App::getLocale())->isoFormat('LL');
    }

    public function getMonthAttribute()
    {
        return Carbon::parse($this->date)->locale(App::getLocale())->isoFormat('MMMM Y');
    }

    public function getValueWithCurrencyAttribute()
    {
        if (config('app.currency_position') === 'before')
        {
            return config('app.currency_symbol') . " ". $this->value;
        }

        return $this->value . " " . config('app.currency_symbol');
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
