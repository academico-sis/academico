<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Enrollment;
use Illuminate\Support\Facades\App;

/**
 * App\Models\ScheduledPayment
 *
 * @property int $id
 * @property int $enrollment_id
 * @property int $value
 * @property string $date
 * @property int|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Enrollment $enrollment
 * @property-read mixed $computed_status
 * @property-read mixed $date_for_humans
 * @property-read mixed $value_with_currency
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereValue($value)
 * @mixin \Eloquent
 */
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
    protected $appends = ['computed_status'];

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

    public function getComputedStatusAttribute()
    {
        // if there is a custom status, always take it
        if ($this->status)
        {
            return $this->status;
        }

        // otherwise, check if the scheduled payment has invoices
        return $this->invoices->count() > 0 ? 2 : 1;
    }

    public function identifiableAttribute()
    {
        return $this->date . " (" . $this->value_with_currency . ")";
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
