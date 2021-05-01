<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $guarded = ['id'];

    public function identifiableAttribute()
    {
        return $this->name;
    }
}
