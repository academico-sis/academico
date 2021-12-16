<?php

namespace App\Models;

use App\Events\StudentDeleting;
use App\Events\StudentUpdated;
use App\Events\UserCreated;
use App\Events\UserDeleting;
use App\Events\UserUpdated;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\CRUD\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use CrudTrait;
    use HasRoles;
    use LogsActivity;

    protected $guarded = ['id'];

    protected static $logFillable = true;

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

    public function getIdnumberAttribute()
    {
        if ($this->isStudent()) {
            return $this->student->idnumber;
        }
    }

    public function getAddressAttribute()
    {
        if ($this->isStudent()) {
            return $this->student->address;
        }
    }

    public function getBirthdateAttribute()
    {
        if ($this->isStudent()) {
            return $this->student->birthdate;
        }
    }

    public function getTeacherIdAttribute()
    {
        return $this->teacher->id ?? null;
    }

    public function getStudentIdAttribute()
    {
        return $this->student->id ?? null;
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
