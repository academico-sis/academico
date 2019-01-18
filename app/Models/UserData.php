<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use App\Models\UserDataRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserData extends Model
{
    protected $table = 'user_data';
    protected $fillable = ['firstname', 'lastname', 'idnumber', 'address', 'email', 'relationship_id', 'user_id'];

    use SoftDeletes;
    use CrudTrait;

    public function phone()
    {
        return $this->morphMany('App\Models\PhoneNumber', 'phoneable');
    }

    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function relationship()
    {
        return $this->belongsTo('App\Models\UserDataRelationship');
    }
}
