<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PhoneNumber extends Model
{
    use LogsActivity;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected static bool $logUnguarded = true;

    public function identifiableAttribute()
    {
        return $this->phone_number;
    }

    public function phoneable()
    {
        return $this->morphTo();
    }
}
