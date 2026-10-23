<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseTime;
use App\Models\Event;
use App\Models\Leave;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Keeps the events (class sessions) of a course in line with its dates, weekly time slots, teacher and room.
 *
 * Changes are applied as differences with the previous settings, and only to the sessions from a given moment:
 * the moment chosen when editing the course, or now by default. Earlier sessions are history: they keep their date,
 * time, teacher and room. Sessions with recorded attendance are never deleted, and sessions that were moved, deleted
 * or reassigned by hand stay as they are unless the setting they come from changes.
 */
class CourseEventScheduler
{
    private ?CarbonInterface $changesApplyFrom = null;

    /** Only change the sessions from this moment on (null: from now on, but create missing sessions at any date) */
    public function applyChangesFrom(?CarbonInterface $moment): void
    {
        $this->changesApplyFrom = $moment;
    }

    /** A time slot was added: create its sessions */
    public function timeSlotCreated(CourseTime $timeSlot): void
    {
        $course = Course::find($timeSlot->course_id);

        $this->createSessions($course, $timeSlot, $this->sessionDates($timeSlot, $course->start_date, $course->end_date, $this->changesApplyFrom));
    }

    /** A time slot moved to another day or time: move its upcoming sessions */
    public function timeSlotUpdated(CourseTime $timeSlot): void
    {
        if (! $timeSlot->wasChanged(['day', 'start', 'end'])) {
            return;
        }

        $from = $this->changesFrom();

        if ($timeSlot->wasChanged('day')) {
            $this->deleteSessions($timeSlot->events()->where('start', '>=', $from));

            $course = Course::find($timeSlot->course_id);
            $this->createSessions($course, $timeSlot, $this->sessionDates($timeSlot, $course->start_date, $course->end_date, $from));

            return;
        }

        // only the time changed: the sessions keep their date and their attendance
        foreach ($timeSlot->events()->where('start', '>=', $from)->get() as $session) {
            $session->update([
                'start' => $session->start->copy()->setTimeFromTimeString($timeSlot->start),
                'end' => $session->start->copy()->setTimeFromTimeString($timeSlot->end),
            ]);
        }
    }

    /** A time slot is about to be removed: delete its upcoming sessions, and keep the others as standalone sessions */
    public function timeSlotDeleting(CourseTime $timeSlot): void
    {
        $this->deleteSessions($timeSlot->events()->where('start', '>=', $this->changesFrom()));

        // the foreign key would delete the remaining sessions (and their attendance) along with the time slot
        $timeSlot->events()->update(['course_time_id' => null]);
    }

    /** The course was updated: apply the changes of dates, teacher, room, name and attendance exemption to its sessions */
    public function courseUpdated(Course $course): void
    {
        if ($course->wasChanged(['start_date', 'end_date'])) {
            $this->moveCourseDates($course);
        }

        $upcomingSessions = fn (): Builder => Event::where('course_id', $course->id)->where('start', '>=', $this->changesFrom());

        // sessions given to a substitute or to another room keep it; unassigned sessions (teacher on leave) are reassigned
        if ($course->wasChanged('teacher_id')) {
            $this->assignTeacher(
                $upcomingSessions()->where(fn (Builder $query) => $query->where('teacher_id', $course->getOriginal('teacher_id'))->orWhereNull('teacher_id')),
                $course->teacher_id,
            );
        }

        if ($course->wasChanged('room_id')) {
            $upcomingSessions()
                ->where(fn (Builder $query) => $query->where('room_id', $course->getOriginal('room_id'))->orWhereNull('room_id'))
                ->update(['room_id' => $course->room_id]);
        }

        if ($course->wasChanged('exempt_attendance')) {
            $upcomingSessions()
                ->where(fn (Builder $query) => $course->getOriginal('exempt_attendance')
                    ? $query->where('exempt_attendance', true)
                    : $query->where('exempt_attendance', false)->orWhereNull('exempt_attendance'))
                ->update(['exempt_attendance' => $course->exempt_attendance]);
        }

        // the name is only a label: rename every session still named after the course
        if ($course->wasChanged('name')) {
            Event::where('course_id', $course->id)
                ->where('name', $course->getOriginal('name'))
                ->update(['name' => $course->name]);
        }
    }

    /** Create the sessions missing from the schedule, and delete those outside it (unless they have attendance) */
    public function resync(Course $course): void
    {
        foreach ($course->times()->get() as $timeSlot) {
            $dates = $this->sessionDates($timeSlot, $course->start_date, $course->end_date);

            $sessionsOutsideSchedule = $timeSlot->events()->get()
                ->reject(fn (Event $session) => $dates->contains($session->start->toDateString()));
            $this->deleteSessions(Event::whereKey($sessionsOutsideSchedule->modelKeys()));

            $this->createSessions($course, $timeSlot, $dates);
        }
    }

    private function moveCourseDates(Course $course): void
    {
        foreach ($course->times()->get() as $timeSlot) {
            $previousDates = $this->sessionDates($timeSlot, $course->getOriginal('start_date'), $course->getOriginal('end_date'));
            $newDates = $this->sessionDates($timeSlot, $course->start_date, $course->end_date);

            $removedDates = $previousDates->diff($newDates);
            $removedSessions = $timeSlot->events()->where('start', '>=', $this->changesFrom())->get()
                ->filter(fn (Event $session) => $removedDates->contains($session->start->toDateString()));
            $this->deleteSessions(Event::whereKey($removedSessions->modelKeys()));

            $addedDates = $newDates->diff($previousDates);
            $this->createSessions($course, $timeSlot, $this->sessionDates($timeSlot, $course->start_date, $course->end_date, $this->changesApplyFrom)->intersect($addedDates));
        }
    }

    /** @param  Collection<int, string>  $dates */
    private function createSessions(Course $course, CourseTime $timeSlot, Collection $dates): void
    {
        $existingDates = $timeSlot->events()->get()->map(fn (Event $session) => $session->start->toDateString());
        $leaveDates = $this->leaveDates($course->teacher_id);

        foreach ($dates->diff($existingDates) as $date) {
            Event::create([
                'course_id' => $course->id,
                'course_time_id' => $timeSlot->id,
                // the session still takes place when the teacher is on leave: it waits for a substitute
                'teacher_id' => $leaveDates->contains($date) ? null : $course->teacher_id,
                'room_id' => $course->room_id,
                'start' => Carbon::parse($date)->setTimeFromTimeString($timeSlot->start),
                'end' => Carbon::parse($date)->setTimeFromTimeString($timeSlot->end),
                'name' => $course->name,
                'exempt_attendance' => $course->exempt_attendance,
            ]);
        }
    }

    private function deleteSessions(Builder|HasMany $sessions): void
    {
        $sessions->whereDoesntHave('attendance')->get()->each->delete();
    }

    private function assignTeacher(Builder $sessions, ?int $teacherId): void
    {
        $leaveDates = $this->leaveDates($teacherId);

        [$sessionsOnLeave, $sessionsToAssign] = $sessions->get()
            ->partition(fn (Event $session) => $leaveDates->contains($session->start->toDateString()));

        Event::whereKey($sessionsToAssign->modelKeys())->update(['teacher_id' => $teacherId]);
        Event::whereKey($sessionsOnLeave->modelKeys())->update(['teacher_id' => null]);
    }

    /**
     * The dates of the sessions of a time slot between two dates, optionally only the sessions from a given moment.
     *
     * @return Collection<int, string>
     */
    private function sessionDates(CourseTime $timeSlot, mixed $start, mixed $end, ?CarbonInterface $from = null): Collection
    {
        if (! $start || ! $end || Carbon::parse($end)->lt(Carbon::parse($start))) {
            return collect();
        }

        return collect(CarbonPeriod::create(Carbon::parse($start)->startOfDay(), Carbon::parse($end)->startOfDay()))
            ->filter(fn (CarbonInterface $date) => $date->dayOfWeek === (int) $timeSlot->day)
            ->filter(fn (CarbonInterface $date) => ! $from || $date->copy()->setTimeFromTimeString($timeSlot->start)->gte($from))
            ->map(fn (CarbonInterface $date) => $date->toDateString())
            ->values();
    }

    /** @return Collection<int, string> */
    private function leaveDates(?int $teacherId): Collection
    {
        if (! $teacherId) {
            return collect();
        }

        return Leave::where('teacher_id', $teacherId)->pluck('date')->map(fn ($date) => Carbon::parse($date)->toDateString());
    }

    private function changesFrom(): CarbonInterface
    {
        return $this->changesApplyFrom ?? Carbon::now(config('settings.courses_timezone'));
    }
}
