<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * @mixin IdeHelperPayment
 */
class Payment extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];

    protected $appends = ['date_for_humans', 'value_with_currency'];

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

    public function getValueAttribute($value)
    {
        return $value / 100;
    }

    public function getEnrollmentNameAttribute(): string
    {
        if ($this->invoice->enrollments()->exists()) {
            return $this->invoice->enrollments->first()->student_name; // TODO fix this, an invoice can theoretically contain several enrollments
        }

        return '';
    }

    public function getIbanAttribute(): string
    {
        if ($this->invoice->enrollments()->exists()) {
            return $this->invoices->enrollments->first()->student->iban ?? '';
        }

        return '';
    }

    public function getBicAttribute(): string
    {
        if ($this->invoice->enrollments()->exists()) {
            return $this->invoices->enrollments->first()->student->bic ?? '';
        }

        return '';
    }

    public function getDateForHumansAttribute()
    {
        if ($this->date) {
            return Carbon::parse($this->date, 'UTC')->locale(App::getLocale())->isoFormat('LL');
        }

        return Carbon::parse($this->created_at, 'UTC')->locale(App::getLocale())->isoFormat('LL');
    }

    public function getMonthAttribute()
    {
        return Carbon::parse($this->date)->locale(App::getLocale())->isoFormat('MMMM Y');
    }

    public function getValueWithCurrencyAttribute()
    {
        if (config('app.currency_position') === 'before') {
            return config('app.currency_symbol').' '.$this->value;
        }

        return $this->value.' '.config('app.currency_symbol');
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
