<?php

namespace App\Models;

use App\Events\UserCreated;
use App\Events\StudentDeleting;
use App\Events\StudentUpdated;
use App\Events\UserDeleting;
use App\Events\UserUpdated;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\CRUD\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use CrudTrait;
    use HasRoles;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['firstname', 'lastname', 'name', 'email', 'password', 'locale', 'preferred_course_view', 'lms_id'];
    protected static $logFillable = true;

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = ['password', 'remember_token'];

    protected $dispatchesEvents = [
        'deleting' => UserDeleting::class,
    ];

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
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
