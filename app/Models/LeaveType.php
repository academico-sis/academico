<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class LeaveType extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];

}
