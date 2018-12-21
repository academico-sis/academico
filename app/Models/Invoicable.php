<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoicable extends Model
{

    use SoftDeletes;

    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function phone()
    {
        return $this->morphMany('App\Models\PhoneNumber', 'phoneable');
    }
}
