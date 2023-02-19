<?php

namespace App\Models;

use App\Models\Interfaces\InvoiceableModel;
use App\Traits\PriceTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Book extends Model implements InvoiceableModel
{
    use CrudTrait;
    use LogsActivity;
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
