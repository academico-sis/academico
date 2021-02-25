<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class InvoiceDetail extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
