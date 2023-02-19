<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PhoneNumber extends Model
{
    use LogsActivity;
    use CrudTrait;

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
