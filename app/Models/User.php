<?php

namespace App\Models;

use App\Events\UserDeleting;
use App\Events\UserUpdated;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
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

    protected static bool $logFillable = true;

    protected $hidden = ['password', 'remember_token'];

    protected $dispatchesEvents = [
        'deleting' => UserDeleting::class,
        'updated' => UserUpdated::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
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

    public function getFirstnameAttribute($value): string
    {
        return Str::title($value);
    }

    public function getLastnameAttribute($value): string
    {
        return Str::upper($value);
    }

    public function getNameAttribute(): string
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
