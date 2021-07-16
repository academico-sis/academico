<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Enrollment;
use Illuminate\Support\Facades\App;

class ScheduledPayment extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'scheduled_payments';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
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

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'enrollment_invoice', 'scheduled_payment_id', 'invoice_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getValueAttribute($value)
    {
        return $value / 100;
    }

    public function getValueWithCurrencyAttribute()
    {
        if (config('app.currency_position') === 'before')
        {
            return config('app.currency_symbol') . " ". $this->value;
        }

        return $this->value . " " . config('app.currency_symbol');
    }

    function getDateForHumansAttribute()
    {
        if ($this->date)
        {
            return Carbon::parse($this->date, 'UTC')->locale(App::getLocale())->isoFormat('LL');
        }
        return Carbon::parse($this->created_at, 'UTC')->locale(App::getLocale())->isoFormat('LL');
    }

    public function computedStatus(): int
    {
        // if there is a custom status, always take it
        if ($this->status)
        {
            return $this->status;
        }

        // otherwise, check if the scheduled payment has invoices
        return $this->invoices->count() > 0 ? 2 : 1;
    }

    public function getDisplayStatusAttribute() : string
    {
        switch ($this->computedStatus())
        {
            case (null):
            case (1):
                return __('Pending');
            case (2):
                return __('Paid');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $value * 100;
    }
}
