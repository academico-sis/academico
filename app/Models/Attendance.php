<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    public function student_data()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
