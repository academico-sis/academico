<?php

namespace App\Models;

use App\Traits\ValueTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use CrudTrait;
    use LogsActivity;
    use ValueTrait;

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

    public function identifiableAttribute()
    {
        return $this->id;
    }
}
