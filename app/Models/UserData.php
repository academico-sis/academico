<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserData extends Model
{
    protected $table = 'user_data';

    use SoftDeletes;

    public function phone()
    {
        return $this->morphMany('App\Models\PhoneNumber', 'phoneable');
    }

}
