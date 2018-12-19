<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{

//    protected $table = 'users';

    public static function get_all_users()
    {
        return \App\Models\BackpackUser::role('prof')->get();
    }


  

}
