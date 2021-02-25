<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use SoftDeletes;
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paidTotal()
    {
        return $this->payments->sum('value');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
