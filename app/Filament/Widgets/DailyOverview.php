<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Teacher;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class DailyOverview extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.daily-overview';

    protected int|string|array $columnSpan = 'full';

    public ?string $selectedDate = null;

    public string $activeTab = 'list';

    /** @var array<int, array<string, mixed>> */
    public array $events = [];

    /** @var array<int, array<string, mixed>> */
    public array $calendarEvents = [];

    /** @var array<int, array<string, mixed>> */
    public array $resources = [];

    /**
     * The classes of every teacher, for users who can view all calendars.
     */
    public static function canView(): bool
    {
        return auth()->user()?->can('calendars.view') ?? false;
    }

    public function mount(): void
    {
        $this->selectedDate = now()->toDateString();
        $this->loadEvents();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function previousDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->toDateString();
        $this->loadEvents();
        $this->dispatch('dailyOverviewEventsUpdated', events: $this->calendarEvents, resources: $this->resources, date: $this->selectedDate);
    }

    public function nextDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->toDateString();
        $this->loadEvents();
        $this->dispatch('dailyOverviewEventsUpdated', events: $this->calendarEvents, resources: $this->resources, date: $this->selectedDate);
    }

    public function today(): void
    {
        $this->selectedDate = now()->toDateString();
        $this->loadEvents();
        $this->dispatch('dailyOverviewEventsUpdated', events: $this->calendarEvents, resources: $this->resources, date: $this->selectedDate);
    }

    protected function loadEvents(): void
    {
        $date = Carbon::parse($this->selectedDate);

        $events = Event::with(['course', 'teacher.user', 'room'])
            ->whereDate('start', $date)
            ->orderBy('start')
            ->get();

        // List view data
        $this->events = $events->map(fn ($event) => [
            'id' => $event->id,
            'title' => $event->course?->name ?? $event->name ?? __('Event'),
            'start' => Carbon::parse($event->start)->format('H:i'),
            'end' => Carbon::parse($event->end)->format('H:i'),
            'color' => $event->color,
            'teacher' => $event->teacher?->user?->name ?? __('Unassigned'),
            'room' => $event->room?->name ?? '',
        ])->toArray();

        // Calendar view data - resources (teachers with events today)
        $teacherIds = $events->pluck('teacher_id')->filter()->unique();

        $this->resources = Teacher::with('user')
            ->whereIn('id', $teacherIds)
            ->get()
            ->map(fn ($teacher) => [
                'id' => $teacher->id,
                'title' => $teacher->user?->firstname ?? __('Unknown'),
            ])
            ->sortBy('title')
            ->values()
            ->toArray();

        // Calendar view data - events with full datetime for FullCalendar
        $this->calendarEvents = $events->map(fn ($event) => [
            'id' => $event->id,
            'title' => $event->course?->name ?? $event->name ?? __('Event'),
            'start' => $event->start,
            'end' => $event->end,
            'resourceId' => $event->teacher_id,
            'backgroundColor' => $event->color,
            'borderColor' => $event->color,
            'extendedProps' => [
                'room' => $event->room?->name ?? '',
            ],
        ])->toArray();
    }
}
