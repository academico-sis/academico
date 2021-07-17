<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $responsable_id
 * @property int $invoice_id
 * @property string $payment_method
 * @property string|null $date
 * @property string $value
 * @property int|null $status
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $bic
 * @property-read mixed $date_for_humans
 * @property-read mixed $display_status
 * @property-read string $enrollment_name
 * @property-read string $iban
 * @property-read mixed $month
 * @property-read mixed $value_with_currency
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereResponsableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereValue($value)
 * @mixin \Eloquent
 */
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
    protected $appends = ['date_for_humans', 'value_with_currency', 'display_status'];
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

    public function getValueAttribute($value)
    {
        return $value / 100;
    }

    public function getEnrollmentNameAttribute(): string
    {
        if ($this->invoice && $this->invoices->first()->enrollment)
        {
            return $this->invoices->first()->enrollment->student_name;
        }

        return '';
    }

    public function getIbanAttribute(): string
    {
        if ($this->invoice && $this->invoices->first()->enrollment)
        {
            return $this->invoices->first()->enrollment->student->iban ?? '';
        }

        return '';
    }

    public function getBicAttribute(): string
    {
        if ($this->invoice && $this->invoices->first()->enrollment)
        {
            return $this->invoices->first()->enrollment->student->bic ?? '';
        }

        return '';
    }

    function getDateForHumansAttribute()
    {
        if ($this->date)
        {
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
        if (config('app.currency_position') === 'before')
        {
            return config('app.currency_symbol') . " ". $this->value;
        }

        return $this->value . " " . config('app.currency_symbol');
    }

    public function getDisplayStatusAttribute()
    {
        switch ($this->status)
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
