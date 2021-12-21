<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * @mixin IdeHelperScheduledPayment
 */
class ScheduledPayment extends Model
{
    use CrudTrait;

    protected $table = 'scheduled_payments';

    protected $guarded = ['id'];

    protected $appends = ['computed_status'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function scopeStatus(Builder $query, $status)
    {
        return match ($status) {
            '2' => $query->where('status', 2)->orWhereHas('invoices'),
            '1' => $query->where('status', 1)->orWhereDoesntHave('invoices'),
            default => $query,
        };
    }

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

    public function statusType()
    {
        return $this->belongsTo(EnrollmentStatusType::class, 'status');
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
        if (config('app.currency_position') === 'before') {
            return config('app.currency_symbol').' '.$this->value;
        }

        return $this->value.' '.config('app.currency_symbol');
    }

    public function getDateForHumansAttribute()
    {
        if ($this->date) {
            return Carbon::parse($this->date, 'UTC')->locale(App::getLocale())->isoFormat('LL');
        }

        return Carbon::parse($this->created_at, 'UTC')->locale(App::getLocale())->isoFormat('LL');
    }

    public function getComputedStatusAttribute()
    {
        // if there is a custom status, always take it
        if ($this->status) {
            return $this->status;
        }

        // otherwise, check if the scheduled payment has invoices
        return $this->invoices->count() > 0 ? 2 : 1;
    }

    public function identifiableAttribute()
    {
        return $this->date.' ('.$this->value_with_currency.')';
    }

    public function getStatusTypeNameAttribute()
    {
        return match ($this->computed_status) {
            2 => __('Paid'),
            1 => __('Pending'),
            default => '-',
        };
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
