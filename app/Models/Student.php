<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Student extends Model implements HasMedia
{
    use CrudTrait;
    use HasMediaTrait;
    use LogsActivity;

    public $timestamps = true;
    protected $guarded = ['id'];
    protected $with = ['user', 'phone'];
    protected $appends = ['email', 'name', 'firstname', 'lastname', 'student_age', 'student_birthdate'];
    protected static $logUnguarded = true;

    protected static function boot()
    {
        parent::boot();

        // when deleting a student, also delete any enrollments, grades and attendance related to this student
        static::deleting(function (self $student) {
            Attendance::where('student_id', $student->id)->delete();
            Enrollment::where('student_id', $student->id)->delete();
        });
    }

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_MAX, 1200, 1200)
            ->optimize();
    }

    /** relations */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function periodAbsences(Period $period = null)
    {
        if ($period == null) {
            $period = Period::get_default_period();
        }

        return $this->hasMany(Attendance::class)
        ->where('attendance_type_id', 4) // absence
        ->whereHas('event', function ($q) use ($period) {
            return $q->whereHas('course', function ($c) use ($period) {
                return $c->where('period_id', $period->id);
            });
        });
    }

    public function periodAttendance(Period $period = null)
    {
        if ($period == null) {
            $period = Period::get_default_period();
        }

        return $this->hasMany(Attendance::class)
        ->whereHas('event', function ($q) use ($period) {
            return $q->whereHas('course', function ($c) use ($period) {
                return $c->where('period_id', $period->id);
            });
        });
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'student_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function phone()
    {
        return $this->morphMany(PhoneNumber::class, 'phoneable');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class)
            ->with('course');
    }

    public function leadType()
    {
        return $this->belongsTo(LeadType::class);
    }

    public function real_enrollments()
    {
        return $this->hasMany(Enrollment::class)
            ->with('course')
            ->whereIn('status_id', ['1', '2'])
            ->whereDoesntHave('childrenEnrollments');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    /** attributes */
    public function getFirstnameAttribute()
    {
        if ($this->user) {
            return $this->user->firstname;
        }
    }

    public function getLastnameAttribute()
    {
        if ($this->user) {
            return $this->user->lastname;
        }
    }

    public function getEmailAttribute()
    {
        if ($this->user) {
            return $this->user->email;
        }
    }

    public function getNameAttribute()
    {
        if ($this->user) {
            return $this->user->firstname.' '.$this->user->lastname;
        }
    }

    public function getStudentAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age;
    }

    public function getStudentBirthdateAttribute()
    {
        return Carbon::parse($this->birthdate)->toFormattedDateString();
    }

    public function getActiveClientsCountAttribute()
    {
        return $this->where('lead_type_id', 1)->count();
    }

    public function getInactiveClientsCountAttribute()
    {
        return $this->where('lead_type_id', 2)->count();
    }

    public function getPotentialClientsCountAttribute()
    {
        return $this->where('lead_type_id', 4)->count();
    }

    public function getFormerClientsCountAttribute()
    {
        return $this->where('lead_type_id', 3)->count();
    }

    /** functions */

    /**
     * enroll the student in a course.
     * If the course has any children, we also enroll the student in the children courses.
     */
    public function enroll(Course $course)
    {
        // avoid duplicates by retrieving an potential existing enrollment for the same course
        $enrollment = Enrollment::firstOrNew([
            'student_id' =>  $this->id,
            'course_id' => $course->id,
        ]);

        $enrollment->responsible_id = backpack_user()->id ?? 1;
        $enrollment->save();

        // if the course has children, enroll in children as well.
        if ($course->children_count > 0) {
            foreach ($course->children as $children_course) {
                $child_enrollment = Enrollment::firstOrNew([
                    'student_id' =>  $this->id,
                    'course_id' => $children_course->id,
                    'parent_id' => $enrollment->id,
                ]);
                $child_enrollment->responsible_id = backpack_user()->id ?? null;
                $child_enrollment->save();
            }
        }

        $this->lead_type_id = 1; // converted
        $this->save();

        // if enabled, subscribe the student to the mailing list
        // TODO this should be extracted to an interface to allow different mailing systems to be used
        if (config('settings.external_mailing_enabled') == true) {
            $api_key = Config::where('name', 'mailerlite_api_key')->first()->value;
            $activeStudentsGroupId = Config::where('name', 'mailerlite_students_group_id')->first()->value;
            $groupsApi = (new \MailerLiteApi\MailerLite($api_key))->groups();
            $addedSubscriber = $groupsApi->addSubscriber($activeStudentsGroupId, [
                'email' => $this->email,
                'name' => $this->firstname,
                'fields' => [
                    'lastname' => $this->lastname,
                ],
            ]); // returns added subscriber
        }

        return $enrollment->id;
    }

    /** SETTERS */
    public function setFirstnameAttribute($value)
    {
        $this->user->firstname = $value;
        $this->user->save();
    }

    public function setLastnameAttribute($value)
    {
        $this->user->lastname = $value;
        $this->user->save();
    }

    public function setEmailAttribute($value)
    {
        $this->user->email = $value;
        $this->user->save();
    }
}
