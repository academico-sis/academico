<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;
    protected $appends = ['total_price_with_currency'];

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function products()
    {
        return $this->hasMany(InvoiceDetail::class)->whereIn('product_type', [Enrollment::class, Fee::class]);
    }

    public function taxes()
    {
        return $this->hasMany(InvoiceDetail::class)->where('product_type', Tax::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paidTotal()
    {
        return $this->payments->sum('value');
    }

    /**
     * Will be deleted in the future, since we decided that one invoice only covers one enrollment?
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrollment()
    {
        return $this->hasOne(Enrollment::class);
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
        $count = Invoice::whereInvoiceTypeId($this->invoice_type_id)->whereYear('created_at', $this->created_at->year)->orderByDesc('invoice_number')->first()->invoice_number;

        $this->update(['invoice_number' => $count + 1]);
    }

    public function getInvoiceReferenceAttribute()
    {
        if (config('invoicing.invoice_numbering') === 'manual')
        {
            return $this->receipt_number;
        }

        return $this->invoiceType->name . $this->created_at->format('y') . "-" . $this->invoice_number;
    }

    public function getInvoiceSeriesAttribute() : string
    {
        return $this->invoiceType->name . $this->created_at->format('y');
    }

    public function getTotalPriceWithCurrencyAttribute()
    {
        if (config('app.currency_position') === 'before')
        {
            return config('app.currency_symbol') . " ". $this->total_price;
        }

        return $this->total_price . " " . config('app.currency_symbol');
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
        if (config('invoicing.invoice_numbering') === 'manual')
        {
            return $this->receipt_number;
        }

        return 'FC' . $this->receipt_number;
    }
}
