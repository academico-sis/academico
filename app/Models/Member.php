<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use CrudTrait;

    protected $table = 'members';

    protected $guarded = ['id'];
}
