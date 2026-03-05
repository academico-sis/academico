<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\Period;
use App\Models\Room;
use BackedEnum;
use Filament\Pages\Page;

class CalendarRoom extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 420;

    public static function canAccess(): bool
    {
        return (auth()->user()?->can('calendars.view') ?? false)
            || ((auth()->user()?->isTeacher() ?? false) && (bool) config('settings.teachers_can_view_calendars'));
    }

    protected string $view = 'filament.pages.calendar-room';

    public ?int $selectedRoomId = null;

    /** @var array<int, array<string, mixed>> */
    public array $rooms = [];

    /** @var array<int, array<string, mixed>> */
    public array $events = [];

    public function mount(): void
    {
        $this->rooms = Room::orderBy('name')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
            ])
            ->toArray();

        if (count($this->rooms) > 0) {
            $this->selectedRoomId = $this->rooms[0]['id'];
        }

        $this->loadEvents();
    }

    public function updatedSelectedRoomId(): void
    {
        $this->loadEvents();
        $this->dispatch('eventsUpdated', events: $this->events);
    }

    protected function loadEvents(): void
    {
        if (! $this->selectedRoomId) {
            $this->events = [];

            return;
        }

        $period = Period::get_default_period();

        $this->events = Event::with(['course', 'teacher.user', 'room'])
            ->when($period, fn ($q) => $q->whereHas('course', fn ($q2) => $q2->where('period_id', $period->id)))
            ->where('room_id', $this->selectedRoomId)
            ->get()
            ->map(fn ($event) => [
                'id' => $event->id,
                'title' => $event->course?->name ?? $event->name ?? 'Event',
                'start' => $event->start,
                'end' => $event->end,
                'color' => $event->course?->color ?? '#3b82f6',
                'teacher' => $event->teacher?->user?->name ?? '',
                'room' => $event->room?->name ?? '',
            ])
            ->toArray();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Calendar');
    }

    public static function getNavigationLabel(): string
    {
        return __('Room Schedule');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Room Schedule');
    }
}
