<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreInvoiceDetail extends Model
{
    public function pre_invoice() {
        return $this->belongsTo('\App\Models\PreInvoice');
    }
}
