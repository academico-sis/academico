<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Student extends Model implements HasMedia
{
    use CrudTrait;
    use InteractsWithMedia;
    use LogsActivity;

    public $timestamps = true;
    protected $guarded = [];
    public $incrementing = false;
    protected $with = ['user', 'phone', 'institution', 'profession'];
    protected $appends = ['email', 'name', 'firstname', 'lastname', 'student_age', 'student_birthdate', 'lead_status', 'is_enrolled'];
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

    public function scopeComputedLeadType($query, $leadTypeId)
    {
        switch ($leadTypeId) {
            case 1: // active / enrolled students
                return $query->whereHas('enrollments', function ($query) {
                    return $query->whereHas('course', function ($q) {
                        $q->where('period_id', Period::get_default_period()->id);
                    });
                });
                break;

            case 2: // custom lead status
            case 3:
                return $query->where('lead_type_id', $leadTypeId);
                break;

            case 4: // old students who have at least one enrollment
                return $query
                    ->where('lead_type_id', $leadTypeId)
                    ->orWhere(function ($query) {
                        $query
                            ->whereNull('lead_type_id')
                            ->whereHas('enrollments', function ($query) {
                                return $query
                                    ->whereHas('course', function ($q) {
                                        $q->where('period_id', '!=', Period::get_default_period()->id);
                                    });
                            })
                            ->whereDoesntHave('enrollments', function ($query) {
                                return $query
                                    ->whereHas('course', function ($q) {
                                        $q->where('period_id', Period::get_default_period()->id);
                                    });
                            });
                    });
                break;
            default:
                return $query;
        }
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_MAX, 1200, 1200)
            ->optimize()->nonQueued();
    }

    /** relations */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function periodAbsences(Period $period = null)
    {
        if ($period == null) {
            $period = $this->currentPeriod;
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
            return Str::title($this->user->firstname);
        }
    }

    public function getLastnameAttribute()
    {
        if ($this->user) {
            return Str::upper($this->user->lastname);
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
            return $this->firstname.' '.$this->lastname;
        }
    }

    public function getStudentAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age ?? '';
    }

    public function getStudentBirthdateAttribute()
    {
        return Carbon::parse($this->birthdate)->locale(App::getLocale())->isoFormat('LL');
    }

    public function getIsEnrolledAttribute()
    {
        // if the student is currently enrolled
        if ($this->enrollments()->whereHas('course', function ($q) {
            return $q->where('period_id', Period::get_default_period()->id);
        })->count() > 0) {
            return 1;
        }
    }

    public function getLeadStatusNameAttribute()
    {
        return LeadType::find($this->lead_status)->name;
    }

    public function getLeadStatusAttribute()
    {
        // if the student is currently enrolled, they are CONVERTED
        if ($this->is_enrolled) {
            return 1;
        }

        // if the student has a special status, return it
        if ($this->leadType != null) {
            return $this->leadType->id;
        }
        // if the student was previously enrolled, they must be potential students
        elseif ($this->has('enrollments')) {
            return 4;
        } else {
            return;
        }
        // otherwise, their status cannot be determined and should be left blank
    }

    /** functions */

    /**
     * enroll the student in a course.
     * If the course has any children, we also enroll the student in the children courses.
     */
    public function enroll(Course $course): int
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

        $this->update(['lead_type_id' => null]); // fallback to default (converted)

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
