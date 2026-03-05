<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\Period;
use App\Models\Teacher;
use BackedEnum;
use Filament\Pages\Page;

class CalendarTeacher extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 410;

    public static function canAccess(): bool
    {
        return (auth()->user()?->can('calendars.view') ?? false)
            || (auth()->user()?->isTeacher() ?? false);
    }

    protected string $view = 'filament.pages.calendar-teacher';

    public ?int $selectedTeacherId = null;

    /** @var array<int, array<string, mixed>> */
    public array $teachers = [];

    /** @var array<int, array<string, mixed>> */
    public array $events = [];

    public function mount(): void
    {
        $this->teachers = Teacher::with('user')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->user?->name ?? 'Teacher #'.$t->id,
            ])
            ->sortBy('name')
            ->values()
            ->toArray();

        if (count($this->teachers) > 0) {
            $this->selectedTeacherId = $this->teachers[0]['id'];
        }

        $this->loadEvents();
    }

    public function updatedSelectedTeacherId(): void
    {
        $this->loadEvents();
        $this->dispatch('eventsUpdated', events: $this->events);
    }

    protected function loadEvents(): void
    {
        if (! $this->selectedTeacherId) {
            $this->events = [];

            return;
        }

        $period = Period::get_default_period();

        $this->events = Event::with(['course', 'teacher.user', 'room'])
            ->when($period, fn ($q) => $q->whereHas('course', fn ($q2) => $q2->where('period_id', $period->id)))
            ->where('teacher_id', $this->selectedTeacherId)
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
        return __('Teacher Schedule');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Teacher Schedule');
    }
}
