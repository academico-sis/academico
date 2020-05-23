<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class PreInvoice extends Model
{
    use SoftDeletes;
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;

    public function pre_invoice_details()
    {
        return $this->hasMany(PreInvoiceDetail::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
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
