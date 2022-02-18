<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperPhoneNumber
 */
class PhoneNumber extends Model
{
    use LogsActivity;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function identifiableAttribute()
    {
        return $this->phone_number;
    }

    public function phoneable()
    {
        return $this->morphTo();
    }
}
