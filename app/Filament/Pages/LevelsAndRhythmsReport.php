<?php

namespace App\Filament\Pages;

use App\Models\Period;
use BackedEnum;
use Filament\Pages\Page;

class LevelsAndRhythmsReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 807;

    protected string $view = 'filament.pages.levels-and-rhythms-report';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('reports.view') ?? false;
    }

    public ?int $selectedPeriodId = null;

    /** @var array<int, array<string, mixed>> */
    public array $levelsData = [];

    /** @var array<string, mixed> */
    public array $levelsChartData = [];

    /** @var array<int, array<string, mixed>> */
    public array $rhythmsData = [];

    /** @var array<string, mixed> */
    public array $rhythmsChartData = [];

    public function mount(): void
    {
        $this->selectedPeriodId = Period::get_default_period()?->id;
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

        $period = Period::find($this->selectedPeriodId);

        if (! $period) {
            return;
        }

        $this->loadLevelsData($period);
        $this->loadRhythmsData($period);
    }

    protected function loadLevelsData(Period $period): void
    {
        $courseGroups = $period->courses()
            ->where('parent_course_id', null)
            ->with('level')
            ->withCount('enrollments')
            ->get()
            ->where('enrollments_count', '>', 0)
            ->groupBy('level.reference');

        $data = [];
        $labels = [];
        $values = [];
        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1'];

        foreach ($courseGroups as $reference => $courses) {
            $levelName = $courses->first()->level->reference ?? __('Other');
            $enrollmentCount = $courses->sum('enrollments_count');
            $taughtHours = $courses->sum('total_volume');

            $soldHours = 0;
            foreach ($courses as $course) {
                $soldHours += $course->total_volume * $course->enrollments()->real()->count();
            }

            $data[] = [
                'level' => $levelName,
                'enrollments' => $enrollmentCount,
                'taught_hours' => $taughtHours,
                'sold_hours' => $soldHours,
            ];

            $labels[] = $levelName;
            $values[] = $enrollmentCount;
        }

        $this->levelsData = $data;
        $this->levelsChartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => array_slice($colors, 0, count($values)),
                ],
            ],
        ];
    }

    protected function loadRhythmsData(Period $period): void
    {
        $courseGroups = $period->courses()
            ->where('parent_course_id', null)
            ->with('rhythm')
            ->withCount('enrollments')
            ->get()
            ->where('enrollments_count', '>', 0)
            ->groupBy('rhythm_id');

        $data = [];
        $labels = [];
        $values = [];
        $colors = ['#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1'];

        foreach ($courseGroups as $rhythmId => $courses) {
            $rhythmName = $courses->first()->rhythm->name ?? __('Other');
            $enrollmentCount = $courses->sum('enrollments_count');

            $data[] = [
                'rhythm' => $rhythmName,
                'enrollments' => $enrollmentCount,
            ];

            $labels[] = $rhythmName;
            $values[] = $enrollmentCount;
        }

        $this->rhythmsData = $data;
        $this->rhythmsChartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => array_slice($colors, 0, count($values)),
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
        return __('Levels & Rhythms Report');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Levels & Rhythms Report');
    }
}
