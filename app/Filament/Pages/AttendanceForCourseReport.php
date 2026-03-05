<?php

namespace App\Filament\Pages;

use App\Models\AttendanceType;
use App\Models\Course;
use App\Models\Period;
use BackedEnum;
use Filament\Pages\Page;

class AttendanceForCourseReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 816;

    public static function canAccess(): bool
    {
        return (auth()->user()?->can('reports.view') ?? false)
            && (bool) config('settings.attendance_reports_enabled');
    }

    protected string $view = 'filament.pages.attendance-for-course-report';

    public ?int $selectedPeriodId = null;

    public ?int $selectedCourseId = null;

    /** @var array<int, array<string, mixed>> */
    public array $availableCourses = [];

    /** @var array<string, mixed> */
    public array $chartData = [];

    public string $courseName = '';

    public function mount(): void
    {
        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;
        $this->loadCourses();
    }

    public function updatedSelectedPeriodId(): void
    {
        $this->selectedCourseId = null;
        $this->loadCourses();
    }

    public function updatedSelectedCourseId(): void
    {
        $this->loadData();
    }

    protected function loadCourses(): void
    {
        if (! $this->selectedPeriodId) {
            return;
        }

        $courses = Course::where('period_id', $this->selectedPeriodId)
            ->has('attendance')
            ->has('events')
            ->orderBy('name')
            ->get();

        $this->availableCourses = $courses->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
        ])->toArray();

        // Auto-select first course
        if (! $this->selectedCourseId && count($this->availableCourses) > 0) {
            $this->selectedCourseId = $this->availableCourses[0]['id'];
        }

        $this->loadData();
    }

    protected function loadData(): void
    {
        if (! $this->selectedCourseId) {
            $this->chartData = [];
            $this->courseName = '';

            return;
        }

        $course = Course::with(['events.attendance'])->find($this->selectedCourseId);

        if (! $course) {
            return;
        }

        $this->courseName = $course->name;

        $labels = $course->events->keys()->map(fn ($k) => __('Event').' '.($k + 1))->toArray();
        $datasets = [];

        foreach (AttendanceType::all() as $type) {
            $data = [];
            foreach ($course->events as $event) {
                $data[] = $event->attendance->where('attendance_type_id', $type->id)->count();
            }

            $datasets[] = [
                'label' => $type->name,
                'data' => $data,
                'backgroundColor' => $type->color ?? '#6b7280',
            ];
        }

        $this->chartData = [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('Attendance per Course');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Attendance per Course');
    }
}
