<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];
}
