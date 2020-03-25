<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    protected $fillable = ['firstname', 'lastname', 'idnumber', 'address', 'email', 'relationship_id', 'student_id'];
    protected $with = ['phone'];

    use SoftDeletes;
    use CrudTrait;

    /*     public function preferredLocale()
        {
            return $this->locale;
        } */

    public function phone()
    {
        return $this->morphMany(PhoneNumber::class, 'phoneable');
    }

    public function getNameAttribute()
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function relationship()
    {
        return $this->belongsTo(ContactRelationship::class);
    }
}
