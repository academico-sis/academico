<?php

namespace App\Models;

use App\Events\CourseCreated;
use App\Events\CourseUpdated;
use App\Traits\PriceTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperCourse
 */
class Course extends Model
{
    use CrudTrait;
    use LogsActivity;
    use PriceTrait;

    protected $dispatchesEvents = [
        'updated' => CourseUpdated::class,
        'created' => CourseCreated::class,
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $with = ['times', 'evaluationType'];

    protected $appends = [
        'course_times',
        'course_teacher_name',
        'course_period_name',
        'course_enrollments_count',
        'accepts_new_students',
        'takes_attendance',
        'sortable_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeHideChildren($query)
    {
        return $query->where('parent_course_id', null);
    }

    public function scopeInternal($query)
    {
        return $query->whereNull('partner_id');
    }

    public function scopeExternal($query)
    {
        return $query->whereNotNull('partner_id');
    }

    public function scopePartner($query, Partner $partner)
    {
        return $query->where('partner_id', $partner->id);
    }

    public function scopeRealcourses($query)
    {
        return $query->doesntHave('children');
    }


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function isInternal()
    {
        return $this->partner_id === null;
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /** the scheduled day/times for the course, that repeat throughout the course date span */
    public function times()
    {
        return $this->hasMany(CourseTime::class, 'course_id');
    }

    /** course sessions (classes) with a specific start and end date/time */
    public function events()
    {
        return $this->hasMany(Event::class)->orderBy('start');
    }

    public function remoteEvents()
    {
        return $this->hasMany(RemoteEvent::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->withTrashed();
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class)->withTrashed();
    }

    public function rhythm()
    {
        return $this->belongsTo(Rhythm::class)->withTrashed();
    }

    public function level()
    {
        return $this->belongsTo(Level::class)->withTrashed();
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    /** children courses = sub-courses, or course modules */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_course_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_course_id');
    }

    /** evaluation methods associated to the course - grades, skill-based evaluation... */
    public function evaluationType()
    {
        return $this->belongsTo(EvaluationType::class);
    }

    /** a Grade model = an individual grade, belongs to a student */
    public function grades()
    {
        return $this->hasManyThrough(Grade::class, Enrollment::class);
    }

    /** the different grade types associated to the course, ie. criteria that will receive the grades */
    public function gradeTypes()
    {
        if ($this->evaluationType) {
            return $this->evaluationType->gradeTypes()->orderBy('order');
        }

        return GradeType::query();
    }

    /** in the case of skills-based evaluation, Skill models are attached to the course
     * This represents the "criteria" that will need to be evaluated to each student (enrollment) in the course.
     */
    public function skills()
    {
        return $this->evaluationType?->skills()->orderBy('order');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

    /**
     * return attendance records associated to the course
     * Since the attendance records are linked to the event, we use a hasManyThrough relation.
     */
    public function attendance()
    {
        return $this->hasManyThrough(Attendance::class, Event::class);
    }

    public function enrollments()
    {
        return $this
            ->hasMany(Enrollment::class, 'course_id', 'id')
            ->whereIn('status_id', Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)
            ->with('student');
    }

    /** returns only pending or paid enrollments, without the child enrollments */
    public function real_enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'id')
            ->whereIn('status_id', Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)
            ->where('parent_id', null);
    }

    // "Partners" are institutions for external courses
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function saveCourseTimes($newCourseTimes)
    {
        // before updating, retrieve existing course times
        $oldCourseTimes = $this->times;

        // check existing coursetimes
        foreach ($oldCourseTimes as $oldCourseTime) {
            $newCourseTime = $newCourseTimes
                ->where('day', $oldCourseTime->day)
                ->where('start', Carbon::parse($oldCourseTime->start)->toTimeString())
                ->where('end', Carbon::parse($oldCourseTime->end)->toTimeString());

            // remove the course time if no longer exists
            if ($newCourseTime->count() == 0) {
                $oldCourseTime->delete();
            }
        }

        foreach ($newCourseTimes as $courseTime) {
            // create missing course times
            if ($this->times()
                    ->where('day', $courseTime['day'])
                    ->where('start', Carbon::parse($courseTime['start'])->toTimeString())
                    ->where('end', Carbon::parse($courseTime['end'])->toTimeString())
                    ->count() == 0) {
                $this->times()->create([
                    'day' => $courseTime['day'],
                    'start' => Carbon::parse($courseTime['start'])->toTimeString(),
                    'end' => Carbon::parse($courseTime['end'])->toTimeString(),
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /**
     * returns the course repeating schedule
     * todo improve this method.
     */
    public function getCourseTimesAttribute()
    {
        $parsedCourseTimes = [];
        // TODO localize these
        $daysInitials = [
            __('Sun'),
            __('Mon'),
            __('Tue'),
            __('Wed'),
            __('Thu'),
            __('Fri'),
            __('Sat'),
        ];

        $courseTimes = null;
        if ($this->times->count() > 0) {
            $courseTimes = $this->times;
        } elseif (($this->children->count() > 0) && ($this->children->first()->times->count() > 0)) {
            $courseTimes = $this->children->first()->times;
        }

        if ($courseTimes) {
            foreach ($courseTimes as $courseTime) {
                $initial = $daysInitials[$courseTime->day];

                if (! isset($parsedCourseTimes[$initial])) {
                    $parsedCourseTimes[$initial] = [];
                }

                $parsedCourseTimes[$initial][] = sprintf(
                    '%s - %s',
                    Carbon::parse($courseTime->start)->locale(App::getLocale())->isoFormat('LT'),
                    Carbon::parse($courseTime->end)->locale(App::getLocale())->isoFormat('LT')
                );
            }
        }

        $result = '';
        foreach ($parsedCourseTimes as $day => $times) {
            $result .= $day.' '.implode(' / ', $times).' | ';
        }

        return trim($result, ' | ');
    }

    public function getCoursePeriodNameAttribute()
    {
        return $this->period->name ?? '';
    }

    public function getCourseTeacherNameAttribute()
    {
        if ($this->teacher_id) {
            return $this->teacher?->name;
        } else {
            return '-';
        }
    }

    public function getShortnameAttribute()
    {
        return Str::slug($this->name);
    }

    public function getDescriptionAttribute()
    {
        return '['.$this->course_period_name.'] - '.$this->name;
    }

    public function getCourseEnrollmentsCountAttribute()
    {
        return $this->enrollments()->count();
    }

    public function getAcceptsNewStudentsAttribute(): bool
    {
        if (! $this->spots || $this->spots == 0) {
            return true;
        }

        return $this->spots - $this->enrollments()->whereIn('status_id', Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)->count() > 0;
    }

    public function getTakesAttendanceAttribute(): bool
    {
        return $this->events_count > 0 && $this->exempt_attendance !== 1 && $this->course_enrollments_count > 0;
    }

    // TODO Rename this attribute to avoid confusions.
    public function getParentAttribute()
    {
        if ($this->parent_course_id !== null) {
            return $this->parent_course_id;
        } else {
            return $this->id;
        }
    }

    public function eventsWithExpectedAttendance()
    {
        return $this->events()->where(function ($query) {
            $query->where('exempt_attendance', '!=', true);
            $query->where('exempt_attendance', '!=', 1);
            $query->orWhereNull('exempt_attendance');
        })->where('start', '<', Carbon::now(config('settings.courses_timezone'))->toDateTimeString());
    }

    public function getSortableIdAttribute()
    {
        if ($this->parent_course_id !== null) {
            return $this->parent_course_id;
        } else {
            return $this->id;
        }
    }

    public function getTotalVolumeAttribute()
    {
        return $this->volume + $this->remote_volume;
    }

    public function price_b(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value / 100,
            set: fn ($value) => $value * 100,
        );
    }

    public function price_c(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value / 100,
            set: fn ($value) => $value * 100,
        );
    }

    public function getFormattedStartDateAttribute()
    {
        return Carbon::parse($this->start_date, 'UTC')->locale(App::getLocale())->isoFormat('LL');
    }

    public function getFormattedEndDateAttribute()
    {
        return Carbon::parse($this->end_date, 'UTC')->locale(App::getLocale())->isoFormat('LL');
    }
}
