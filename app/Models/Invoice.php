<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperInvoice
 */
class Invoice extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];

    protected static $logUnguarded = true;

    protected $appends = ['total_price_with_currency', 'formatted_date'];

    protected $casts = [
        'date' => 'date',
    ];

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class)->orderByRaw("CASE WHEN product_type like '%Enrollment' THEN 10 WHEN product_type like '%Fee' THEN 5 ELSE 0 END desc");
    }

    public function products()
    {
        return $this->hasMany(InvoiceDetail::class)->whereIn('product_type', [Enrollment::class, Fee::class]);
    }

    public function taxes()
    {
        return $this->hasMany(InvoiceDetail::class)->where('product_type', Tax::class);
    }

    public function scheduledPayments()
    {
        return $this->belongsToMany(ScheduledPayment::class, 'enrollment_invoice', 'invoice_id', 'scheduled_payment_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paidTotal()
    {
        return $this->payments->sum('value');
    }

    public function enrollments()
    {
        return $this->belongsToMany(Enrollment::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function invoiceType()
    {
        return $this->belongsTo(InvoiceType::class);
    }

    public function setNumber()
    {
        // retrieve the last entry for the same type / year, and increment
        $count = self::whereInvoiceTypeId($this->invoice_type_id)->whereYear('created_at', $this->created_at->year)->orderByDesc('invoice_number')->first()->invoice_number;

        $this->update(['invoice_number' => $count + 1]);
    }

    public function getInvoiceReferenceAttribute()
    {
        if (config('invoicing.invoice_numbering') === 'manual') {
            return $this->receipt_number;
        }

        return $this->invoiceType->name.$this->created_at->format('y').'-'.$this->invoice_number;
    }

    public function getInvoiceSeriesAttribute() : string
    {
        return $this->invoiceType->name.$this->created_at->format('y');
    }

    public function getTotalPriceWithCurrencyAttribute()
    {
        if (config('app.currency_position') === 'before') {
            return config('app.currency_symbol').' '.$this->total_price;
        }

        return $this->total_price.' '.config('app.currency_symbol');
    }

    public function getTotalPriceAttribute($value)
    {
        return $value / 100;
    }

    public function setTotalPriceAttribute($value)
    {
        $this->attributes['total_price'] = $value * 100;
    }

    public function getFormattedNumberAttribute()
    {
        if (config('invoicing.invoice_numbering') === 'manual') {
            return $this->receipt_number;
        }

        return 'FC'.$this->receipt_number;
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->date)->locale(app()->getLocale())->isoFormat('Do MMMM YYYY');
    }
}
