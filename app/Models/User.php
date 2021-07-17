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
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $username
 * @property string $firstname
 * @property string $lastname
 * @property string|null $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $api_token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $locale
 * @property string|null $preferred_course_view
 * @property int|null $lms_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read mixed $address
 * @property-read mixed $birthdate
 * @property-read mixed $force_update
 * @property-read mixed $idnumber
 * @property-read mixed $name
 * @property-read mixed $student_id
 * @property-read mixed $teacher_id
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Student|null $student
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLmsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePreferredCourseView($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
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
    protected $fillable = ['firstname', 'lastname', 'name', 'username', 'email', 'password', 'locale', 'preferred_course_view', 'lms_id'];
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
