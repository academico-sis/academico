<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\InvoiceDetail
 *
 * @property int $id
 * @property int $invoice_id
 * @property string $product_name
 * @property string|null $product_code
 * @property int|null $product_id
 * @property string|null $product_type
 * @property int $quantity
 * @property string $price
 * @property string $tax_rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read mixed $price_with_currency
 * @property-read mixed $total_price
 * @property-read \App\Models\Invoice $invoice
 * @property-read Model|\Eloquent $product
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail newQuery()
 * @method static \Illuminate\Database\Query\Builder|InvoiceDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|InvoiceDetail withTrashed()
 * @method static \Illuminate\Database\Query\Builder|InvoiceDetail withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $final_price
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereFinalPrice($value)
 */
class InvoiceDetail extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;
    protected $appends = ['price_with_currency'];


    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->morphTo();
    }

    public function getPriceAttribute($value)
    {
        return $value / 100;
    }

    public function getFinalPriceAttribute($value)
    {
        return $value ? $value / 100 : $this->price;
    }

    public function getTotalPriceAttribute($value)
    {
        return ($value * $this->quantity )/ 100;
    }

    public function getPriceWithCurrencyAttribute()
    {
        if (config('app.currency_position') === 'before')
        {
            return config('app.currency_symbol') . " ". $this->price;
        }

        return $this->price . " " . config('app.currency_symbol');
    }

    /*
|--------------------------------------------------------------------------
| MUTATORS
|--------------------------------------------------------------------------
*/

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }
}
