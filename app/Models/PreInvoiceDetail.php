<?php

namespace App\Models;

use App\Models\PreInvoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreInvoiceDetail extends Model
{
    use SoftDeletes;

    public function pre_invoice() {
        return $this->belongsTo(PreInvoice::class);
    }
}
