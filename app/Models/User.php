<?php

namespace App\Models;

use App\Models\Student;
use App\Models\Teacher;
use Backpack\CRUD\CrudTrait;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Backpack\Base\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use CrudTrait;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['firstname', 'lastname', 'email', 'password', 'locale'];


    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = ['password', 'remember_token'];


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
        return (Teacher::where('user_id', backpack_user()->id)->count() > 0);
    }

    public function isStudent()
    {
        return (Student::where('user_id', backpack_user()->id)->count() > 0);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;   
    }
    
    public function getIdnumberAttribute()
    {
        if ($this->isStudent())
        {
            return $this->student->idnumber;
        }
    }

    public function getAddressAttribute()
    {
        if ($this->isStudent())
        {
        return $this->student->address;
        }
    }

    public function getBirthdateAttribute()
    {
        if ($this->isStudent())
        {
        return $this->student->birthdate;
        }
    }

    public function getTeacherIdAttribute()
    {
        return $this->teacher->id ?? null;
    }

    public function getForceUpdateAttribute()
    {
        return $this->force_update ?? null;
    }
}
