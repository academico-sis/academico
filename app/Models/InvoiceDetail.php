<?php

namespace App\Models;

use App\Traits\PriceTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperInvoiceDetail
 */
class InvoiceDetail extends Model
{
    use SoftDeletes;
    use LogsActivity;
    use CrudTrait;
    use PriceTrait;

    protected $guarded = ['id'];

    protected $appends = ['price_with_currency'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the parent invoiceable model
     */
    public function product()
    {
        return $this->morphTo();
    }

    public function getFinalPriceAttribute($value)
    {
        return $value ? $value / 100 : $this->price;
    }

    public function getTotalPriceAttribute($value)
    {
        return ($value * $this->quantity) / 100;
    }

    public function identifiableAttribute() {
        return $this->id;
    }
}
