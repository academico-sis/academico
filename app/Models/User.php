<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Spatie\Permission\Traits\HasRoles;
use Tightenco\Parental\HasParentModel;
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
    use HasParentModel;

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


/*     public function preferredLocale()
    {
        return $this->locale;
    } */
    
    public function isTeacher()
    {
        return (Teacher::where('user_id', backpack_user()->id)->count() > 0);
    }

    public function isStudent()
    {
        return (Student::where('user_id', backpack_user()->id)->count() > 0);
    }
}
