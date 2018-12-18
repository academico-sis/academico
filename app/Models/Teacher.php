<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles;
use Backpack\CRUD\CrudTrait;

class Teacher extends \App\User
{

    protected $table = 'users';

/*     public static function teachers()
    {
        return \App\User::role('admin')->get();
    } */


  

}
