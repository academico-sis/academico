<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;

class PreInvoice extends Model
{
    use SoftDeletes;

    public function details()
    {
        return $this->hasMany('PreInvoiceDetail');
    }
    
}
