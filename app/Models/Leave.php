<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Leave extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];

    public function leave_type()
    {
        return $this->belongsTo('App\Models\LeaveType');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
