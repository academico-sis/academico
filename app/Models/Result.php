<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }
}
