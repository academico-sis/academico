<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\CrudTrait;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use Backpack\Base\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;
use Tightenco\Parental\HasParentModel;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use CrudTrait;
    use HasRoles;
    use HasParentModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'email', 'password', 'birthdate', 'genre_id', 'language', 'idnumber', 'address'
        ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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



    public function scopeTeacher($query)
    {
        return $query->role('teacher')->get();
    }

    public function scopeStudent($query)
    {
        return $query->role('student')->get();
    }

    public function attendance()
    {
        return $this->hasMany('App\Models\Attendance');
    }

     /**
     * enroll the student in a course.
     * If the course has any children, we also enroll the student in the children courses.
     */
    public function enroll(Course $course)
    {
        // avoid duplicates by retrieving an potential existing enrollment for the same course
        $enrollment = Enrollment::firstOrNew([
            'user_id' =>  $this->id,
            'course_id' => $course->id
        ]);
        $enrollment->responsible_id = backpack_user()->id ?? 1; // todo fix: phpunit does not seem to access backpack_user()
        $enrollment->save();
        
        // if the course has children, enroll in children as well.
        if($course->children_count > 0)
        {
            foreach($course->children as $children_course)
            {
                $child_enrollment = Enrollment::firstOrNew([
                    'user_id' =>  $this->id,
                    'course_id' => $children_course->id,
                    'parent_id' => $enrollment->id
                ]);
                $child_enrollment->responsible_id = backpack_user()->id;
                $child_enrollment->save();
            }
        }

        return $enrollment->id;
    }

    public function additional_data()
    {
        return $this->hasMany('App\Models\UserData');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }
    
    public function phone()
    {
        return $this->morphMany('App\Models\PhoneNumber', 'phoneable');
    }

    public function getFirstnameAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function getLastnameAttribute($value)
    {
        return strtoupper($value);
    }

    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getStudentAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age;
    }

    public function getStudentBirthdateAttribute()
    {
        return Carbon::parse($this->birthdate)->toFormattedDateString();
    }

    public function enrollments()
    {
        return $this->hasMany('App\Models\Enrollment')
            ->with('course_data');
    }

    public function courses()
    {
        return $this->hasMany('App\Models\Course', 'teacher_id');
    }

    public function getCurrentCoursesAttribute()
    {
        $period = Period::get_default_period();

        return $this->courses()
            ->where('period_id', $period->id)
            ->where('end_date', '>', (new Carbon)->toDateTimeString())
            ->withCount('children')
            ->withCount('enrollments')
            ->get()
            ->where('children_count', 0);
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event', 'teacher_id');
    }

    public function period_planned_hours(Period $period)
    {
        return $this
            ->hasMany('App\Models\Event', 'teacher_id')
            ->get()
            ->where('start', '>=', Carbon::parse($period->start)->setTime(0, 0, 0)->toDateTimeString())
            ->where('end', '<=', Carbon::parse($period->end)->setTime(23, 59, 0)->toDateTimeString())
            ->sum('length');
    }

    public function period_worked_hours(Period $period)
    {
        return $this
            ->hasMany('App\Models\Event', 'teacher_id')
            ->get()
            ->where('start', '>=', Carbon::parse($period->start)->setTime(0, 0, 0)->toDateTimeString())
            ->where('end', '<=', Carbon::parse($period->end)->setTime(23, 59, 0)->toDateTimeString())
            ->where('end', '<=', (new Carbon)->toDateTimeString())
            ->sum('length');
    }

    public function remote_events()
    {
        return $this->hasMany('App\Models\RemoteEvent');
    }

    public function periodRemoteHours(Period $period)
    {
        return $this
            ->hasMany('App\Models\RemoteEvent')
            ->where('period_id', $period->id)
            ->get()
            ->sum('worked_hours');
    }

    public function period_max_hours(Period $period)
    {
        return 250; // will be editable in the future
    }


    public function getEventsWithPendingAttendanceAttribute()
    {
        return $this->events()
            ->where(function($query) {
                $query->where('exempt_attendance', '!=', true);
                $query->where('exempt_attendance', '!=', 1);
                $query->orWhereNull('exempt_attendance');
            })
            ->where('course_id', '!=', null)
            ->with('attendance')
            ->with('course.enrollments')
            ->where('start', '<', (new Carbon)->toDateTimeString()) // todo check timezones
            ->get()
            ->filter(function ($event, $key) {
                return $event->course->enrollments->count() != $event->attendance()->count();
            });
    }

}
