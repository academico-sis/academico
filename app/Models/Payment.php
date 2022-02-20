<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @mixin IdeHelperPayment
 */
class Payment extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];

    protected $appends = ['date_for_humans', 'value_with_currency'];

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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function paymentmethod()
    {
        return $this->belongsTo(Paymentmethod::class, 'payment_method', 'code')->withDefault();
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
        if (config('academico.currency_position') === 'before') {
            return config('academico.currency_symbol').' '.$this->value;
        }

        return $this->value.' '.config('academico.currency_symbol');
    }

    protected function value(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value / 100,
            set: fn ($value) => $value * 100,
        );
    }

    public function identifiableAttribute() {
        return $this->id;
    }
}
