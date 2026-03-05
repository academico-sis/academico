<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\Course;
use App\Models\Event;
use App\Models\Period;
use BackedEnum;
use Filament\Pages\Page;

class AttendanceReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 812;

    public static function canAccess(): bool
    {
        return (auth()->user()?->can('reports.view') ?? false)
            && (bool) config('settings.attendance_reports_enabled');
    }

    protected string $view = 'filament.pages.attendance-report';

    public ?int $selectedPeriodId = null;

    /** @var array<int, array<string, mixed>> */
    public array $coursesData = [];

    /** @var array<string, mixed> */
    public array $chartData = [];

    /** @var array<int, array<string, mixed>> */
    public array $attendanceTypes = [];

    public function mount(): void
    {
        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;

        $this->attendanceTypes = AttendanceType::all()
            ->map(fn ($type) => [
                'id' => $type->id,
                'name' => $type->name,
            ])
            ->toArray();

        $this->loadData();
    }

    public function updatedSelectedPeriodId(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        if (! $this->selectedPeriodId) {
            return;
        }

        $courses = Course::where('period_id', $this->selectedPeriodId)
            ->internal()
            ->whereHas('events')
            ->orderBy('name')
            ->get();

        $courseIds = $courses->pluck('id');
        $eventIds = Event::whereIn('course_id', $courseIds)->pluck('id');

        $attendanceCounts = Attendance::whereIn('event_id', $eventIds)
            ->with('event')
            ->get()
            ->groupBy(fn ($a) => $a->event?->course_id)
            ->map(fn ($group) => $group->groupBy('attendance_type_id')->map->count());

        $courseData = [];
        $labels = [];
        $datasets = [];

        $colors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'];

        foreach ($this->attendanceTypes as $index => $type) {
            $datasets[$type['id']] = [
                'label' => $type['name'],
                'data' => [],
                'backgroundColor' => $colors[$index % count($colors)],
            ];
        }

        foreach ($courses as $course) {
            $counts = $attendanceCounts->get($course->id, collect());
            $total = $counts->sum();

            $row = [
                'name' => $course->name,
                'total' => $total,
            ];

            $labels[] = $course->name;

            foreach ($this->attendanceTypes as $type) {
                $count = $counts->get($type['id'], 0);
                $row['type_'.$type['id']] = $count;
                $datasets[$type['id']]['data'][] = $count;
            }

            $courseData[] = $row;
        }

        $this->coursesData = $courseData;
        $this->chartData = [
            'labels' => $labels,
            'datasets' => array_values($datasets),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('Attendance Report');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Attendance Report');
    }
}
