<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function result_name()
    {
        return $this->belongsTo('\App\Models\ResultType', 'result_type_id');
    }
    
}
