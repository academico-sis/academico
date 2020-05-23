<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;

class Course extends Model
{
    use CrudTrait;
    use SoftDeletes;
    use LogsActivity;

    /** model events */
    protected static function boot()
    {
        parent::boot();

        // when a course model is updated
        static::updated(function (self $course) {

        // if course dates have changed, sync all events
            if ($course->isDirty('start_date') || $course->isDirty('end_date')) {
                Log::info('cleaning the events after course date change');
                // delete events before course start date
                Event::where('course_id', $course->id)->where('start', '<', $course->start_date)->delete();

                // delete events after course end date
                Event::where('course_id', $course->id)->where('end', '>', $course->end_date)->delete();

                // create events before first existing event and after course start
                $firstEvent = $course->events()->orderBy('start')->first();

                $start = Carbon::parse($course->start_date)->startOfDay();
                $end = Carbon::parse($firstEvent->start)->startOfDay();

                // for each day before the first event
                while ($start < $end) {
                    // if there is a coursetime for today, create the event
                    if ($course->times->contains('day', $start->format('w'))) {
                        Event::create([
                            'course_id' => $course->id,
                            'teacher_id' => $course->teacher_id,
                            'room_id' => $course->room_id,
                            'start' => $start->setTimeFromTimeString($course->times->where('day', $start->format('w'))->first()->start)->toDateTimeString(),
                            'end' => $start->setTimeFromTimeString($course->times->where('day', $start->format('w'))->first()->end)->toDateTimeString(),
                            'name' => $course->name,
                            'course_time_id' => $course->times->where('day', $start->format('w'))->first()->id,
                            'exempt_attendance' => $course->exempt_attendance,
                        ]);
                    }
                    $start->addDay();
                }

                // create events after last existing event and before course end
                $lastEvent = $course->events()->orderBy('start', 'desc')->first();

                $start = Carbon::parse($lastEvent->end)->endOfDay();
                $end = Carbon::parse($course->end_date)->endOfDay();

                // for each day after the last event
                while ($start < $end) {
                    // if there is a coursetime for today, create the event
                    if ($course->times->contains('day', $start->format('w'))) {
                        Event::create([
                            'course_id' => $course->id,
                            'teacher_id' => $course->teacher_id,
                            'room_id' => $course->room_id,
                            'start' => $start->setTimeFromTimeString($course->times->where('day', $start->format('w'))->first()->start)->toDateTimeString(),
                            'end' => $start->setTimeFromTimeString($course->times->where('day', $start->format('w'))->first()->end)->toDateTimeString(),
                            'name' => $course->name,
                            'course_time_id' => $course->times->where('day', $start->format('w'))->first()->id,
                            'exempt_attendance' => $course->exempt_attendance,
                        ]);
                    }
                    $start->addDay();
                }
            }

            // update course events with new room and teacher
            Event::where('course_id', $course->id)->update(['room_id' => $course->room_id]);
            Event::where('course_id', $course->id)->update(['teacher_id' => $course->teacher_id]);
        });
    }

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
    //protected $with = ['enrollments'];
    protected $appends = ['course_times', 'course_teacher_name', 'course_enrollments_count', 'sortable_id'];
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
        return $this->hasMany(\App\Models\CourseTime::class, 'course_id');
    }

    /** course sessions (classes) with a specific start and end date/time */
    public function events()
    {
        return $this->hasMany(\App\Models\Event::class);
    }

    /** may be null if the teacher is not yet assigned */
    public function teacher()
    {
        return $this->belongsTo(\App\Models\Teacher::class)->withTrashed();
    }

    public function campus()
    {
        return $this->belongsTo(\App\Models\Campus::class);
    }

    /** may be null if the room is not yet assigned */
    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class)->withTrashed();
    }

    /** the "category" of course */
    public function rhythm()
    {
        return $this->belongsTo(\App\Models\Rhythm::class);
    }

    /** a course can only have one level. Parent courses would generally have no level defined */
    public function level()
    {
        return $this->belongsTo(\App\Models\Level::class);
    }

    /** a course needs to belong to a period */
    public function period()
    {
        return $this->belongsTo(\App\Models\Period::class);
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
    public function evaluation_type()
    {
        return $this->belongsToMany(\App\Models\EvaluationType::class);
    }

    /** a Grade model = an individual grade, belongs to a student */
    public function grades()
    {
        return $this->hasMany(\App\Models\Grade::class)->with('student');
    }

    /** the different grade types associated to the course */
    public function grade_type()
    {
        return $this->belongsToMany(\App\Models\GradeType::class);
    }

    /** in the case of skills-based evaluation, Skill models are attached to the course */
    public function skills()
    {
        return $this->belongsToMany(\App\Models\Skills\Skill::class)->orderBy('order');
    }

    public function skill_evaluations()
    {
        return $this->hasMany(\App\Models\Skills\SkillEvaluation::class)
        ->with('skill_scale');
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
        return $this->hasManyThrough(\App\Models\Attendance::class, \App\Models\Event::class);
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
        $days = '';
        $times = '';

        if ($this->times->count() > 0) {
            foreach ($this->times->unique('day') as $time) {
                if ($time->day == '1') {
                    $days .= 'L';
                }
                if ($time->day == '2') {
                    $days .= 'M';
                }
                if ($time->day == '3') {
                    $days .= 'X';
                }
                if ($time->day == '4') {
                    $days .= 'J';
                }
                if ($time->day == '5') {
                    $days .= 'V';
                }
                if ($time->day == '6') {
                    $days .= 'S';
                }
                if ($time->day == '0') {
                    $days .= 'D';
                }
            }

            foreach ($this->times->unique('start') as $time) {
                $times .= Carbon::parse($time->start)->format('g:i').' - '.Carbon::parse($time->end)->format('g:i');
            }
        } elseif ($this->children->count() > 0) {
            foreach ($this->children->first()->times->unique('day') as $time) {
                if ($time->day == '1') {
                    $days .= 'L';
                }
                if ($time->day == '2') {
                    $days .= 'M';
                }
                if ($time->day == '3') {
                    $days .= 'X';
                }
                if ($time->day == '4') {
                    $days .= 'J';
                }
                if ($time->day == '5') {
                    $days .= 'V';
                }
                if ($time->day == '6') {
                    $days .= 'S';
                }
                if ($time->day == '0') {
                    $days .= 'D';
                }
            }

            foreach ($this->children->first()->times->unique('start') as $time) {
                $times .= Carbon::parse($time->start)->format('g:i').' - '.Carbon::parse($time->end)->format('g:i');
            }
        }

        return $days.' - '.$times;
    }

    public function getCourseRoomNameAttribute()
    {
        return strtoupper($this->room['name']);
    }

    public function getCourseLevelNameAttribute()
    {
        return $this->level['name'];
    }

    public function getCourseRhythmNameAttribute()
    {
        return strtoupper($this->rhythm['name']);
    }

    public function getCourseTeacherNameAttribute()
    {
        if ($this->teacher_id) {
            return $this->teacher['firstname'].' '.$this->teacher['lastname'];
        } else {
            return '-';
        }
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
