<?php

namespace App\Models;

use App\Models\Interfaces\InvoiceableModel;
use App\Traits\PriceTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Book extends Model implements InvoiceableModel
{
    use HasFactory, LogsActivity;
    use PriceTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $appends = ['price_with_currency', 'type'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function getTypeAttribute(): string
    {
        return 'book';
    }
}
