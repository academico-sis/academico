<?php

namespace App\Models;

use App\Events\StudentCreated;
use App\Events\StudentDeleting;
use App\Events\StudentUpdated;
use App\Events\TeacherCreated;
use App\Events\TeacherUpdated;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Teacher extends Model
{
    use CrudTrait;
    use SoftDeletes;
    use LogsActivity;

    public $timestamps = true;
    protected $guarded = [];
    public $incrementing = false;
    protected $with = ['user'];
    protected $appends = ['firstname', 'lastname', 'name', 'email'];
    protected static $logUnguarded = true;

    protected $dispatchesEvents = [
        'created' => TeacherCreated::class,
        'updated' => TeacherUpdated::class,
    ];

    /** relations */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id')->withTrashed();
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
        return $this->user->email ?? "";
    }

    public function getNameAttribute()
    {
        if ($this->user) {
            return $this->user->firstname.' '.$this->user->lastname;
        }
    }

    public function period_courses(Period $period)
    {
        return $this->courses()
            ->where('period_id', $period->id)
            ->withCount('children')
            ->withCount('enrollments')
            ->get()
            ->where('children_count', 0);
    }

    public function period_events(Period $period)
    {
        return $this->events
            ->where('start', '>=', Carbon::parse($period->start)->setTime(0, 0, 0)->toDateTimeString())
            ->where('end', '<=', Carbon::parse($period->end)->setTime(23, 59, 0)->toDateTimeString());
    }

    public function period_remote_events(Period $period)
    {
        return $this->remote_events
            ->where('period_id', $period->id);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function remote_events()
    {
        return $this->hasMany(RemoteEvent::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class)->with('leaveType');
    }

    public function getUpcomingLeavesAttribute()
    {
        $dates = $this->leaves->where('date', '>=', Carbon::now()->format('Y-m-d'))->sortBy('date')->values()->all();
        if (count($dates) == 0) {
            return [];
        }
        $formatted_leaves = [];
        $range_start = Carbon::parse($dates[0]['date']);

        // loop through all leave dates
        for ($i = 0; $i < count($dates); $i++) {

            // if the next date does not touch current range
            if (isset($dates[$i + 1])) {
                if (Carbon::parse($dates[$i]['date'])->addDay() != Carbon::parse($dates[$i + 1]['date'])) {
                    // push the range to result array
                    $range_end = Carbon::parse($dates[$i]['date']);
                    if ($range_start == $range_end) {
                        array_push($formatted_leaves, $range_start->format('d/m/Y'));
                    } else {
                        array_push($formatted_leaves, $range_start->format('d/m/Y').' - '.$range_end->format('d/m/Y'));
                    }

                    $range_start = Carbon::parse($dates[$i + 1]['date']);
                }
            } else {
                // if there is no further date
                $range_end = Carbon::parse($dates[$i]['date']);
                if ($range_start == $range_end) {
                    array_push($formatted_leaves, $range_start->format('d/m/Y'));
                } else {
                    array_push($formatted_leaves, $range_start->format('d/m/Y').' - '.$range_end->format('d/m/Y'));
                }
            }
        }

        return $formatted_leaves;
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function plannedHoursInPeriod($start, $end)
    {
        return $this->events()
            ->where('start', '>=', Carbon::parse($start)->setTime(0, 0, 0)->toDateTimeString())
            ->where('end', '<=', Carbon::parse($end)->setTime(23, 59, 0)->toDateTimeString())
            ->get()
            ->sum('length');
    }

    /* Return the events with incomplete attendance for this teacher */
    public function events_with_pending_attendance(Period $period)
    {
        $eventsWithMissingAttendance = [];

        $eventsWithExpectedAttendance = $this->events()
        ->where(function ($query) {
            $query->where('exempt_attendance', '!=', true);
            $query->where('exempt_attendance', '!=', 1);
            $query->orWhereNull('exempt_attendance');
        })
        ->where('course_id', '!=', null)
        ->whereHas('course', function (Builder $query) use ($period) {
            return $query->where('period_id', $period->id)
                ->where(function ($query) {
                    $query->where('exempt_attendance', '!=', true);
                    $query->where('exempt_attendance', '!=', 1);
                    $query->orWhereNull('exempt_attendance');
                });
        })
        ->with('course')
        ->where('start', '<', Carbon::now(config('settings.courses_timezone'))->addMinutes(20)->toDateTimeString())
        ->get();

        foreach ($eventsWithExpectedAttendance as $event) {
            foreach ($event->enrollments as $enrollment) {

                // if a student has no attendance record for the class (event)
                $hasNotAttended = $event->attendance->where('student_id', $enrollment->student_id)->isEmpty();

                // count one and break loop
                if ($hasNotAttended) {
                    $eventsWithMissingAttendance[] = $event;
                    break;
                }
            }
        }

        return collect($eventsWithMissingAttendance);
    }
}
