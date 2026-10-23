<?php

namespace App\Models;

use App\Events\UserDeleting;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use HasRoles;
    use LogsActivity;
    use Notifiable;
    use SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['admin', 'secretary', 'viewer']) || $this->isTeacher();
    }

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token'];

    protected $dispatchesEvents = [
        'deleting' => UserDeleting::class,
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

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'id', 'id');
    }

    public function teacher(): HasOne
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

    /**
     * Emails are unique and used to log in. They are stored in lower case, and an empty email is stored as null,
     * so that users without email do not conflict.
     */
    public function setEmailAttribute(?string $value): void
    {
        $value = trim((string) $value);

        $this->attributes['email'] = $value === '' ? null : Str::lower($value);
    }
}
