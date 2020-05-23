<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class PreInvoiceDetail extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;

    public function pre_invoice()
    {
        return $this->belongsTo(PreInvoice::class);
    }
}
