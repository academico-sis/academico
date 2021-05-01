<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use SoftDeletes;
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;
    protected $appends = ['total_price_with_currency'];

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class);
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
}
