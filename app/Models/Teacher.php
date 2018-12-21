<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{

//    protected $table = 'users';
    use SoftDeletes;

    public static function get_all_users()
    {
        return \App\Models\BackpackUser::role('prof')->get();
    }


  

}
