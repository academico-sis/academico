<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use App\Models\ContactRelationship;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Translation\HasLocalePreference;

class Contact extends Model
{
    protected $table = 'contacts';
    protected $fillable = ['firstname', 'lastname', 'idnumber', 'address', 'email', 'relationship_id', 'student_id'];

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
        return $this->firstname . ' ' . $this->lastname;
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
