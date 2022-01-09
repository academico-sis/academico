<?php

namespace App\Models;

use App\Events\InvoiceDeleting;
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

    protected static bool $logUnguarded = true;

    protected $appends = ['total_price_with_currency', 'formatted_date'];

    protected $casts = [
        'date' => 'date',
    ];

    protected $dispatchesEvents = [
        'deleting' => InvoiceDeleting::class,
    ];

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class)->orderByRaw("CASE WHEN product_type like '%Enrollment' THEN 10 WHEN product_type like '%Fee' THEN 5 ELSE 0 END desc");
    }

    public function taxes()
    {
        return $this->hasMany(InvoiceDetail::class)->where('product_type', Tax::class);
    }

    public function scheduledPayments()
    {
        return $this->hasMany(InvoiceDetail::class)->where('product_type', ScheduledPayment::class);
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
        return $this->hasMany(InvoiceDetail::class)->where('product_type', Enrollment::class);
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
            return config('app.currency_symbol').' '.$this->totalPrice();
        }

        return $this->totalPrice().' '.config('app.currency_symbol');
    }

    public function totalPrice()
    {
        return $this->invoiceDetails()->sum('price') / 100;
    }

    public function getTotalPriceAttribute()
    {
        return $this->totalPrice();
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

    public function getBalanceAttribute()
    {
        return $this->totalPrice() - $this->paidTotal();
    }
}
