<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Profession;
use App\Models\Institution;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Image\Manipulations;

class Student extends Model implements HasMedia
{

    use CrudTrait;
    use SoftDeletes;
    use HasMediaTrait;

    public $timestamps = true;
    protected $guarded = ['id'];
    
    
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
        ->whereHas('event', function($q) use ($period) {
            return $q->whereHas('course', function($c) use ($period) {
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
        ->whereHas('event', function($q) use ($period) {
            return $q->whereHas('course', function($c) use ($period) {
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
        return $this->user->firstname;
    }

    public function getLastnameAttribute()
    {
        return $this->user->lastname;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function getNameAttribute()
    {
        return $this->user->firstname . ' ' . $this->user->lastname;
    }

    public function getStudentAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age;
    }

    public function getStudentBirthdateAttribute()
    {
        return Carbon::parse($this->birthdate)->toFormattedDateString();
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
            'course_id' => $course->id
        ]);

        $enrollment->responsible_id = backpack_user()->id ?? 1;
        $enrollment->save();
        
        // if the course has children, enroll in children as well.
        if($course->children_count > 0)
        {
            foreach($course->children as $children_course)
            {
                $child_enrollment = Enrollment::firstOrNew([
                    'student_id' =>  $this->id,
                    'course_id' => $children_course->id,
                    'parent_id' => $enrollment->id
                ]);
                $child_enrollment->responsible_id = backpack_user()->id ?? null;
                $child_enrollment->save();
            }
        }

        $this->lead_type_id = 1; // converted
        $this->save();
        return $enrollment->id;
    }
}
