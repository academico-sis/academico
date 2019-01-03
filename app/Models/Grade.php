<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    public function grade_type()
    {
        return $this->belongsTo('\App\Models\GradeType');
    }

    public function student()
    {
        return $this->belongsTo('\App\Models\User', 'user_id');
    }
}
