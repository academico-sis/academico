<?php

namespace App\Models;

use App\Events\UserDeleting;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use CrudTrait;
    use HasRoles;
    use LogsActivity;

    protected $guarded = ['id'];

    protected static bool $logFillable = true;

    protected $hidden = ['password', 'remember_token'];

    protected $dispatchesEvents = [
        'deleting' => UserDeleting::class,
    ];

    public function getEmailForPasswordReset() : string
    {
        return $this->email;
    }

    public function isTeacher()
    {
        return Teacher::whereId($this->id)->count() > 0;
    }

    public function isStudent()
    {
        return Student::whereId($this->id)->count() > 0;
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'id', 'id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'id', 'id');
    }

    public function getFirstnameAttribute($value)
    {
        return Str::title($value);
    }

    public function getLastnameAttribute($value)
    {
        return Str::upper($value);
    }

    public function getNameAttribute()
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function getForceUpdateAttribute()
    {
        return $this->force_update ?? null;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }
}
