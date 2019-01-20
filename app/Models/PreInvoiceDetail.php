<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreInvoiceDetail extends Model
{
    use SoftDeletes;

    public function pre_invoice() {
        return $this->belongsTo('App\Models\PreInvoice');
    }
}
