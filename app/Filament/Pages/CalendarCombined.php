<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\Leave;
use App\Models\Period;
use App\Models\Room;
use App\Models\Teacher;
use BackedEnum;
use Filament\Pages\Page;

class CalendarCombined extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()?->can('calendars.view') ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?int $navigationSort = 400;

    protected string $view = 'filament.pages.calendar-combined';

    public const UNASSIGNED_KEY = 'unassigned';

    /** @var array<int|string> */
    public array $selectedTeacherIds = [];

    /** @var array<int> */
    public array $selectedRoomIds = [];

    /** @var array<int, array<string, mixed>> */
    public array $teachers = [];

    /** @var array<int, array<string, mixed>> */
    public array $rooms = [];

    /** @var array<int, array<string, mixed>> */
    public array $events = [];

    /** @var array<string, string> */
    public array $teacherColors = [];

    public function mount(): void
    {
        $this->teachers = Teacher::with('user')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->user?->name ?? __('Teacher').' #'.$t->id,
            ])
            ->sortBy('name')
            ->values()
            ->toArray();

        $this->rooms = Room::orderBy('name')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
            ])
            ->toArray();

        $colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1'];
        foreach ($this->teachers as $i => $teacher) {
            $this->teacherColors[$teacher['id']] = $colors[$i % count($colors)];
        }

        $this->loadEvents();
    }

    public function updatedSelectedTeacherIds(): void
    {
        $this->loadEvents();
        $this->dispatch('eventsUpdated', events: $this->events);
    }

    public function updatedSelectedRoomIds(): void
    {
        $this->loadEvents();
        $this->dispatch('eventsUpdated', events: $this->events);
    }

    protected function loadEvents(): void
    {
        $teacherIds = array_map('intval', array_filter($this->selectedTeacherIds, fn ($id) => $id !== self::UNASSIGNED_KEY));
        $roomIds = array_map('intval', $this->selectedRoomIds);
        $includeUnassigned = in_array(self::UNASSIGNED_KEY, $this->selectedTeacherIds);

        if (empty($teacherIds) && empty($roomIds) && ! $includeUnassigned) {
            $this->events = [];

            return;
        }

        $period = Period::get_default_period();

        $events = Event::with(['course', 'teacher.user', 'room'])
            ->when($period, fn ($q) => $q->whereHas('course', fn ($q2) => $q2->where('period_id', $period->id)))
            ->where(function ($q) use ($teacherIds, $roomIds, $includeUnassigned) {
                if (! empty($teacherIds)) {
                    $q->orWhereIn('teacher_id', $teacherIds);
                }
                if ($includeUnassigned) {
                    $q->orWhereNull('teacher_id');
                }
                if (! empty($roomIds)) {
                    $q->orWhereIn('room_id', $roomIds);
                }
            })
            ->get()
            ->unique('id')
            ->map(fn ($event) => [
                'id' => $event->id,
                'title' => $event->course?->name ?? $event->name ?? __('Event'),
                'start' => $event->start,
                'end' => $event->end,
                'color' => $this->teacherColors[$event->teacher_id] ?? ($event->course?->color ?? '#3b82f6'),
                'teacher' => $event->teacher?->user?->name ?? '',
                'room' => $event->room?->name ?? '',
                'allDay' => false,
            ])
            ->values()
            ->toArray();

        $leaveEvents = $this->loadLeaveEvents($teacherIds, $period);

        $this->events = array_merge($events, $leaveEvents);
    }

    /** @return array<int, array<string, mixed>> */
    protected function loadLeaveEvents(array $teacherIds, ?Period $period): array
    {
        if (empty($teacherIds)) {
            return [];
        }

        $query = Leave::with(['teacher.user', 'leaveType'])
            ->whereIn('teacher_id', $teacherIds);

        if ($period) {
            $query->whereBetween('date', [$period->start, $period->end]);
        }

        return $query->get()
            ->map(fn (Leave $leave) => [
                'id' => 'leave-'.$leave->id,
                'title' => __('Leave').': '.($leave->teacher?->user?->name ?? '').' ('.$leave->leaveType->name.')',
                'start' => $leave->date,
                'end' => $leave->date,
                'color' => '#ef4444',
                'teacher' => $leave->teacher?->user?->name ?? '',
                'room' => '',
                'allDay' => true,
            ])
            ->values()
            ->toArray();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Organization');
    }

    public static function getNavigationLabel(): string
    {
        return __('Combined Schedule');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Combined Schedule');
    }
}
