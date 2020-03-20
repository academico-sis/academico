<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function phoneable()
    {
        return $this->morphTo();
    }
}
