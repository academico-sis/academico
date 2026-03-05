<?php

namespace App\Filament\Pages;

use App\Services\DateRange;
use App\Services\StatService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;

class ExternalSummaryReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 832;

    public static function canAccess(): bool
    {
        return (auth()->user()?->can('reports.view') ?? false)
            && (bool) config('settings.external_courses_enabled');
    }

    protected string $view = 'filament.pages.external-summary-report';

    public ?string $startDate = null;

    public ?string $endDate = null;

    /** @var array<string, int> */
    public array $summaryData = [];

    /** @var array<int, array<string, mixed>> */
    public array $coursesData = [];

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->loadData();
    }

    public function updatedStartDate(): void
    {
        $this->loadData();
    }

    public function updatedEndDate(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        if (! $this->startDate || ! $this->endDate) {
            return;
        }

        $dateRange = new DateRange(Carbon::parse($this->startDate), Carbon::parse($this->endDate));
        $stats = new StatService(external: true, reference: $dateRange);

        $this->summaryData = [
            'courses' => $stats->coursesCount(),
            'enrollments' => $stats->enrollmentsCount(),
            'students' => $stats->studentsCount(),
            'taught_hours' => $stats->taughtHoursCount(),
            'sold_hours' => $stats->soldHoursCount(),
        ];

        $this->coursesData = $stats->coursesQuery->with(['partner', 'rhythm', 'level', 'teacher'])
            ->orderBy('start_date')
            ->get()
            ->map(fn ($course) => [
                'name' => $course->name,
                'partner' => $course->partner?->name ?? '-',
                'rhythm' => $course->rhythm?->name ?? '-',
                'level' => $course->level?->name ?? '-',
                'teacher' => $course->course_teacher_name,
                'start_date' => $course->start_date?->format('Y-m-d') ?? '-',
                'end_date' => $course->end_date?->format('Y-m-d') ?? '-',
                'head_count' => $course->head_count ?? 0,
                'volume' => $course->total_volume,
            ])->toArray();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('External Summary');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('External Summary Report');
    }
}
