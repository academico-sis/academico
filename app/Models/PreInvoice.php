<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PreInvoice extends Model
{
    use SoftDeletes;


    public function pre_invoice_details()
    {
        return $this->hasMany('\App\Models\PreInvoiceDetail');
    }

    public function enrollments()
    {
        return $this->belongsToMany('\App\Models\Enrollment', 'enrollment_pre_invoice', 'enrollment_id', 'pre_invoice_id');
    }
    
}
