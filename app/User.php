<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\CrudTrait;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use CrudTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function enroll(\App\Models\Course $course)
    {
        $enrollment = new \App\Models\Enrollment;
        $enrollment->user_id = $this->id;
        $enrollment->responsible_id = 1;
        $enrollment->course_id = $course->id;
        $enrollment->status = 1;

        $enrollment->save();
    }

    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
