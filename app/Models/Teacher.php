<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{

//    protected $table = 'users';
    use SoftDeletes;

    public static function all($columns = ['*'])
    {
        return \App\Models\User::role('teacher')->get();
    }

}
