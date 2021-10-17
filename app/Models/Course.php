<?php

namespace App\Models;

use App\Events\CourseCreated;
use App\Events\CourseUpdated;
use App\Models\Skills\Skill;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Partner;

/**
 * App\Models\Course
 *
 * @property int $id
 * @property int $campus_id
 * @property int|null $rhythm_id
 * @property int|null $level_id
 * @property int $volume
 * @property string $name
 * @property string $price
 * @property string|null $hourly_price
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int|null $room_id
 * @property int|null $teacher_id
 * @property int|null $parent_course_id
 * @property int|null $exempt_attendance
 * @property int $period_id
 * @property int|null $opened
 * @property int|null $spots
 * @property int|null $head_count
 * @property int|null $new_students
 * @property string|null $color
 * @property int|null $evaluation_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $partner_id
 * @property string|null $remote_volume
 * @property int|null $sync_to_lms
 * @property int|null $lms_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Book[] $books
 * @property-read int|null $books_count
 * @property-read \App\Models\Campus $campus
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \App\Models\EvaluationType|null $evaluationType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @property-read bool $accepts_new_students
 * @property-read mixed $course_enrollments_count
 * @property-read mixed $course_level_name
 * @property-read mixed $course_period_name
 * @property-read mixed $course_rhythm_name
 * @property-read mixed $course_room_name
 * @property-read mixed $course_teacher_name
 * @property-read mixed $course_times
 * @property-read mixed $description
 * @property-read mixed $formatted_end_date
 * @property-read mixed $formatted_start_date
 * @property-read Course|null $parent
 * @property-read mixed $pending_attendance
 * @property-read mixed $price_with_currency
 * @property-read mixed $shortname
 * @property-read mixed $sortable_id
 * @property-read bool $takes_attendance
 * @property-read mixed $total_volume
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Grade[] $grades
 * @property-read int|null $grades_count
 * @property-read \App\Models\Level|null $level
 * @property-read Partner|null $partner
 * @property-read \App\Models\Period $period
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $real_enrollments
 * @property-read int|null $real_enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RemoteEvent[] $remoteEvents
 * @property-read int|null $remote_events_count
 * @property-read \App\Models\Rhythm|null $rhythm
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\Teacher|null $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CourseTime[] $times
 * @property-read int|null $times_count
 * @method static \Illuminate\Database\Eloquent\Builder|Course children()
 * @method static \Illuminate\Database\Eloquent\Builder|Course external()
 * @method static \Illuminate\Database\Eloquent\Builder|Course internal()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course parent()
 * @method static \Illuminate\Database\Eloquent\Builder|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course realcourses()
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCampusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereEvaluationTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereExemptAttendance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereHeadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereHourlyPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereLmsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereNewStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereOpened($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereParentCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course wherePartnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course wherePeriodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereRemoteVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereRhythmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSpots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSyncToLms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereVolume($value)
 * @mixin \Eloquent
 */
class Course extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $dispatchesEvents = [
        'updated' => CourseUpdated::class,
        'created' => CourseCreated::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    // protected $primaryKey = 'id';
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = [];
    // protected $hidden = [];
    protected $dates = ['start_date', 'end_date'];
    protected $with = ['evaluationType'];
    protected $appends = [
        'course_times',
        'course_teacher_name',
        'course_period_name',
        'course_enrollments_count',
        'accepts_new_students',
        'takes_attendance',
        'sortable_id'
    ];

    protected static $logUnguarded = true;

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /** filter only the courses that have no parent */
    public function scopeParent($query)
    {
        return $query->where('parent_course_id', null);
    }

    public function scopeChildren($query)
    {
        return $query->where('parent_course_id', '!=', null);
    }

    public function scopeInternal($query)
    {
        return $query->where('campus_id', 1);
    }

    public function scopeExternal($query)
    {
        return $query->where('campus_id', 2);
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

    /** returns all courses that are open for enrollments */
    public static function get_available_courses(Period $period)
    {
        return self::where('period_id', $period->id)
        ->where('campus_id', 1)
        ->with('times')
        ->with('teacher')
        ->with('room')
        ->with('rhythm')
        ->with('level')
        ->withCount('enrollments')
        ->get();
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

    /** may be null if the teacher is not yet assigned */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->withTrashed();
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /** may be null if the room is not yet assigned */
    public function room()
    {
        return $this->belongsTo(Room::class)->withTrashed();
    }

    /** the "category" of course */
    public function rhythm()
    {
        return $this->belongsTo(Rhythm::class)->withTrashed();
    }

    /** a course can only have one level. Parent courses would generally have no level defined */
    public function level()
    {
        return $this->belongsTo(Level::class)->withTrashed();
    }

    /** a course needs to belong to a period */
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
    public function grade_types()
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

    /**
     * Return events for which the attendance records do not match the course student count.
     *
     * todo - optimize this method (reduce the number of queries and avoid the foreach loop)
     * but filtering the collection increases the number of DB queries... (why ?)
     */
    public function getPendingAttendanceAttribute()
    {
        $events = Event::where(function ($query) {
            $query->where('course_id', $this->id);
            $query->where('exempt_attendance', '!=', true);
            $query->where('exempt_attendance', '!=', 1);
            $query->orWhereNull('exempt_attendance');
        })
        ->where('course_id', '!=', null)
        ->with('attendance')
        ->with('teacher')
        ->with('course.enrollments')
        ->where('start', '<', Carbon::now(config('settings.courses_timezone'))->toDateTimeString())
        ->get();

        $pending_events = [];

        foreach ($events as $event) {
            // if the attendance record count do not match the enrollment count, push the event to array
            $pending_attendance = $event->course->enrollments->count() - $event->attendance->count();

            if ($pending_attendance != 0) {
                $pending_events[$event->id]['event'] = $event->name ?? '';
                $pending_events[$event->id]['event_id'] = $event->id;
                $pending_events[$event->id]['course_id'] = $event->course_id;
                $pending_events[$event->id]['event_date'] = Carbon::parse($event->start)->toDateString();
                $pending_events[$event->id]['teacher'] = $event->teacher->name ?? '';
                $pending_events[$event->id]['pending'] = $pending_attendance ?? '';
            }
        }

        return $pending_events;
    }

    public function enrollments()
    {
        return $this
            ->hasMany(Enrollment::class, 'course_id', 'id')
            ->whereIn('status_id', [1, 2]) // pending or paid enrollments only
            ->with('student');
    }

    /** returns only pending or paid enrollments, without the child enrollments */
    public function real_enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'id')
        ->whereIn('status_id', ['1', '2']) // pending or paid
        ->where('parent_id', null);
    }

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
                    ->where('day', $courseTime->day)
                    ->where('start', Carbon::parse($courseTime->start)->toTimeString())
                    ->where('end', Carbon::parse($courseTime->end)->toTimeString())
                    ->count() == 0) {
                $this->times()->create([
                    'day' => $courseTime->day,
                    'start' => Carbon::parse($courseTime->start)->toTimeString(),
                    'end' => Carbon::parse($courseTime->end)->toTimeString(),
                ]);
            }
        }
    }

    public function saveRemoteEvents($events)
    {
        $this->remoteEvents()->delete();
        foreach ($events as $event)
        {
            $this->remoteEvents()->create([
                'name' => $event->name ?? $this->name,
                'worked_hours' => $event->worked_hours,
            ]);
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

        $parsed = [];
        $times = '';
        $group = 1;
        $dayTimes = [];

        foreach ($parsedCourseTimes as $day => $times) {
            $dayTimes[] = [
                'day' => $day,
                'time' => implode(' / ', $times),
            ];
        }

        // grouping by time
        foreach ($dayTimes as $key => $value) {
            if ($value['time'] == $times || !$times) {
                $parsed[$group][] = $value;
            } else {
                $group++;
                $parsed[$group][] = $value;
            }
            $times = $value['time'];
        }

        // handle for case Mon - Friday 9:00am - 5:00pm but on the Wed 10:00am - 2:00pm, so will split up like so [Mon - Tue 9:00am - 5:00pm | Wed 10:00am -2:00pm | Thur - Friday 9:00am - 5:00pm]
        $result = collect($parsed)->map(function ($value) {
            $value = collect($value);
            $day = $value->first()['day'];
            $times = $value->first()['time'];
            if ($value->count() > 1) {
                $day .= ' - ' . $value->last()['day'];
            }

            return "$day $times";
        })->implode(' | ');

        return $result;
    }

    public function getCourseRoomNameAttribute()
    {
        return strtoupper($this->room->name);
    }

    public function getCourseLevelNameAttribute() : string
    {
        if ($this->level->exists()) {
            return $this->level->name;
        }

        return '';
    }

    public function getCourseRhythmNameAttribute()
    {
        return strtoupper($this->rhythm->name);
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
        return '[' . $this->course_period_name . '] - ' . $this->name;
    }

    public function getChildrenCountAttribute()
    {
        return self::where('parent_course_id', $this->id)->count();
    }

    public function getChildrenAttribute()
    {
        return self::where('parent_course_id', $this->id)->get();
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

        return $this->spots - $this->course_enrollments_count > 0;
    }

    public function getTakesAttendanceAttribute(): bool
    {
        return $this->events_count > 0 && $this->exempt_attendance !== 1 && $this->course_enrollments_count > 0;
    }

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

    public function getTotalVolumeAttribute() {
        return $this->volume + $this->remote_volume;
    }

    public function getPriceAttribute($value)
    {
        return $value / 100;
    }

    public function getPriceWithCurrencyAttribute()
    {
        if (config('app.currency_position') === 'before')
        {
            return config('app.currency_symbol') . " ". $this->price;
        }

        return $this->price . " " . config('app.currency_symbol');
    }


    public function getFormattedStartDateAttribute()
    {
        return Carbon::parse($this->start_date, 'UTC')->locale(App::getLocale())->isoFormat('LL');
    }

    public function getFormattedEndDateAttribute()
    {
        return Carbon::parse($this->end_date, 'UTC')->locale(App::getLocale())->isoFormat('LL');
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }
}
