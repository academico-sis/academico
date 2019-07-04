<?php

namespace App\Models;

use App\Models\PreInvoice;
use Illuminate\Database\Eloquent\Model;

class PreInvoiceDetail extends Model
{

    protected $guarded = ['id'];

    
    public function pre_invoice() {
        return $this->belongsTo(PreInvoice::class);
    }
}
