<?php

namespace App\Models;

use App\Events\InvoiceDeleting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    protected $appends = ['total_price_with_currency', 'formatted_date'];

    protected $casts = [
        'date' => 'date',
    ];

    protected $dispatchesEvents = [
        'deleting' => InvoiceDeleting::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function invoiceDetails(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class)->orderByRaw("CASE WHEN product_type like '%Enrollment' THEN 10 WHEN product_type like '%Fee' THEN 5 ELSE 0 END desc");
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class)->where('product_type', Tax::class);
    }

    public function scheduledPayments(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class)->where('product_type', ScheduledPayment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paidTotal(): float
    {
        return $this->payments->sum('value');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class)->where('product_type', Enrollment::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function invoiceType(): BelongsTo
    {
        return $this->belongsTo(InvoiceType::class);
    }

    public function setNumber()
    {
        $last = self::whereInvoiceTypeId($this->invoice_type_id)
            ->whereYear('created_at', $this->created_at->year)
            ->orderByDesc('invoice_number')
            ->first();

        $this->update(['invoice_number' => ($last->invoice_number ?? 0) + 1]);
    }

    public function getInvoiceReferenceAttribute()
    {
        if (config('invoicing.invoice_numbering') === 'manual') {
            return $this->receipt_number;
        }

        return $this->invoiceType->name.$this->created_at->format('y').'-'.$this->invoice_number;
    }

    public function getInvoiceSeriesAttribute(): string
    {
        return $this->invoiceType->name.$this->created_at->format('y');
    }

    public function getTotalPriceWithCurrencyAttribute()
    {
        if (config('academico.currency_position') === 'before') {
            return config('academico.currency_symbol').' '.$this->totalPrice();
        }

        return $this->totalPrice().' '.config('academico.currency_symbol');
    }

    public function totalPrice()
    {
        $total = 0;
        foreach ($this->invoiceDetails as $invoiceDetail) {
            $total += $invoiceDetail->quantity * $invoiceDetail->price;
        }

        return $total;
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
