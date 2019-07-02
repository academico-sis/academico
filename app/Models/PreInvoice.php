<?php

namespace App\Models;

use App\Models\Enrollment;
use Backpack\CRUD\CrudTrait;
use App\Models\PreInvoiceDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PreInvoice extends Model
{
    use SoftDeletes;
    use CrudTrait;

    protected $guarded = ['id'];

    public function pre_invoice_details()
    {
        return $this->hasMany(PreInvoiceDetail::class);
    }

    public function enrollments()
    {
        return $this->belongsToMany(Enrollment::class, 'enrollment_pre_invoice', 'pre_invoice_id', 'enrollment_id');
    }
    
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    
}
