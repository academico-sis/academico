<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\CrudTrait;


class PreInvoice extends Model
{
    use SoftDeletes;
    use CrudTrait;

    public function pre_invoice_details()
    {
        return $this->hasMany('App\Models\PreInvoiceDetail');
    }

    public function enrollments()
    {
        return $this->belongsToMany('App\Models\Enrollment', 'enrollment_pre_invoice', 'pre_invoice_id', 'enrollment_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
}
