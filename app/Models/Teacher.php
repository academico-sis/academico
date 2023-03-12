<?php

namespace App\Models;

use App\Events\TeacherUpdated;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
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

    protected $dispatchesEvents = [
        'updated' => TeacherUpdated::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    /** relations */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id')->withTrashed();
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

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class)->with('leaveType');
    }

    public function getUpcomingLeavesAttribute()
    {
        $dates = $this->leaves->where('date', '>=', Carbon::now()->format('Y-m-d'))->sortBy('date')->values()->all();
        if ((is_countable($dates) ? count($dates) : 0) == 0) {
            return [];
        }
        $formatted_leaves = [];
        $range_start = Carbon::parse($dates[0]['date']);

        // loop through all leave dates
        for ($i = 0; $i < (is_countable($dates) ? count($dates) : 0); $i++) {

            // if the next date does not touch current range
            if (isset($dates[$i + 1])) {
                if (Carbon::parse($dates[$i]['date'])->addDay() != Carbon::parse($dates[$i + 1]['date'])) {
                    // push the range to result array
                    $range_end = Carbon::parse($dates[$i]['date']);
                    if ($range_start == $range_end) {
                        $formatted_leaves[] = $range_start->format('d/m/Y');
                    } else {
                        $formatted_leaves[] = $range_start->format('d/m/Y').' - '.$range_end->format('d/m/Y');
                    }

                    $range_start = Carbon::parse($dates[$i + 1]['date']);
                }
            } else {
                // if there is no further date
                $range_end = Carbon::parse($dates[$i]['date']);
                if ($range_start == $range_end) {
                    $formatted_leaves[] = $range_start->format('d/m/Y');
                } else {
                    $formatted_leaves[] = $range_start->format('d/m/Y').' - '.$range_end->format('d/m/Y');
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

    public function plannedRemoteHoursInPeriod($start, $end)
    {
        $total = 0;
        /** @var Course $course */
        foreach ($this->courses()->realcourses()->whereDate('start_date', '<=', $end)->get() as $course) {
            $courseRemoteVolumePerWeek = $course->remote_volume / max(1, $course->end_date->diffInWeeks($course->start_date) + 1);

            // the number of days (selected period) overlapping the course length
            // latest of course and report start dates.
            $startDate = Carbon::parse($course->start_date)->max($start);

            // only process if the course ends AFTER the start date
            if ($startDate <= $course->end_date) {
                $endDate = Carbon::parse($course->end_date)->min($end);

                // add 1 to include current week.
                $numberOfWeeks = $startDate->diffInWeeks($endDate) + 1;

                $total += $courseRemoteVolumePerWeek * $numberOfWeeks;
            }
        }

        return $total;
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
        ->whereHas('course', fn (Builder $query) => $query->where('period_id', $period->id)
            ->where(function ($query) {
                $query->where('exempt_attendance', '!=', true);
                $query->where('exempt_attendance', '!=', 1);
                $query->orWhereNull('exempt_attendance');
            }))
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

    public function firstname(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? Str::title($this->user->firstname) : '',
        );
    }

    public function lastname(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? Str::title($this->user->lastname) : '',
        );
    }

    public function email(): Attribute
    {
        return new Attribute(
            get: fn (): ?string => $this?->user?->email,
        );
    }

    public function name(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? "{$this->firstname} {$this->lastname}" : '',
        );
    }
}
