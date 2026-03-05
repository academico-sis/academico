<?php

namespace App\Filament\Pages;

use App\Models\Course;
use App\Models\Period;
use BackedEnum;
use Filament\Pages\Page;

class CoursesReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 810;

    protected string $view = 'filament.pages.courses-report';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('reports.view') ?? false;
    }

    public ?int $selectedPeriodId = null;

    /** @var array<int, array<string, mixed>> */
    public array $coursesData = [];

    /** @var array<string, mixed> */
    public array $chartData = [];

    public function mount(): void
    {
        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;
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

        $courses = Course::withCount('real_enrollments')
            ->where('period_id', $this->selectedPeriodId)
            ->internal()
            ->orderByDesc('real_enrollments_count')
            ->get();

        $this->coursesData = $courses->map(fn (Course $course) => [
            'name' => $course->name,
            'enrollments' => $course->real_enrollments_count,
            'rhythmName' => $course->rhythm?->name ?? '-',
            'levelName' => $course->level?->name ?? '-',
            'teacherName' => $course->course_teacher_name,
            'totalVolume' => $course->total_volume,
        ])->toArray();

        $this->chartData = [
            'labels' => $courses->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => __('Enrollments'),
                    'data' => $courses->pluck('real_enrollments_count')->toArray(),
                    'backgroundColor' => '#f59e0b',
                ],
            ],
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('Courses Report');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Courses Report');
    }
}
