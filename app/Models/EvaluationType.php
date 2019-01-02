<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationType extends Model
{
    public $timestamps = false;


    public function courses()
    {
        return $this->belongsToMany('\App\Models\Course');
    }
}
