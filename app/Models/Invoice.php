<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Invoice
 *
 * @property int $id
 * @property int|null $invoice_number
 * @property int|null $invoice_type_id
 * @property string|null $client_name
 * @property string|null $client_idnumber
 * @property string|null $client_address
 * @property string|null $client_email
 * @property string|null $client_phone
 * @property string|null $total_price
 * @property int $company_id
 * @property string|null $receipt_number
 * @property \Illuminate\Support\Carbon|null $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @property-read mixed $formatted_date
 * @property-read mixed $formatted_number
 * @property-read mixed $invoice_reference
 * @property-read string $invoice_series
 * @property-read mixed $total_price_with_currency
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceDetail[] $invoiceDetails
 * @property-read int|null $invoice_details_count
 * @property-read \App\Models\InvoiceType|null $invoiceType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceDetail[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScheduledPayment[] $scheduledPayments
 * @property-read int|null $scheduled_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceDetail[] $taxes
 * @property-read int|null $taxes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientIdnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereReceiptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedAt($value)
 * @mixin \Eloquent
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

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->date)->locale(app()->getLocale())->isoFormat('Do MMMM YYYY');
    }
}
