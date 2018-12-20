<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoicable extends Model
{
    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function phone()
    {
        return $this->morphMany('App\Models\PhoneNumber', 'phoneable');
    }
}
