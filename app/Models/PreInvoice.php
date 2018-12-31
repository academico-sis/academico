<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PreInvoiceDetail;

class PreInvoice extends Model
{
    use SoftDeletes;

    public function pre_invoice_details()
    {
        return $this->hasMany('\App\Models\PreInvoiceDetail');
    }
    
}
