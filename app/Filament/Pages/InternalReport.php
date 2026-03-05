<?php

namespace App\Filament\Pages;

use App\Models\Config;
use App\Models\Period;
use App\Models\Year;
use App\Services\StatService;
use BackedEnum;
use Filament\Pages\Page;

class InternalReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 805;

    protected string $view = 'filament.pages.internal-report';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('reports.view') ?? false;
    }

    public ?int $startFromPeriodId = null;

    /** @var array<int, array<string, mixed>> */
    public array $allPeriods = [];

    /** @var array<int, array<string, mixed>> */
    public array $reportData = [];

    /** @var array<string, mixed> */
    public array $chartData = [];

    public function mount(): void
    {
        $this->allPeriods = Period::withoutGlobalScopes()
            ->orderBy('year_id')
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])
            ->toArray();

        // Default: use the first_period from config, or the very first period
        $configFirstPeriod = Config::where('name', 'first_period')->first();
        $this->startFromPeriodId = $configFirstPeriod?->value
            ?? ($this->allPeriods[0]['id'] ?? null);

        $this->loadData();
    }

    public function updatedStartFromPeriodId(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        if (! $this->startFromPeriodId) {
            return;
        }

        $periods = Period::withoutGlobalScopes()
            ->where('id', '>=', $this->startFromPeriodId)
            ->orderBy('year_id')
            ->orderBy('order')
            ->orderBy('id')
            ->with('year')
            ->get();

        $chartLabels = [];
        $chartStudents = [];
        $chartEnrollments = [];
        $rows = [];

        $groupedByYear = $periods->groupBy('year_id');

        foreach ($groupedByYear as $yearId => $yearPeriods) {
            $yearEnrollments = 0;
            $yearTaughtHours = 0;
            $yearSoldHours = 0;
            $year = $yearPeriods->first()->year;

            $periodRows = [];

            foreach ($yearPeriods as $period) {
                $stats = new StatService(external: false, partner: null, reference: $period);

                $enrollments = $stats->enrollmentsCount();
                $students = $stats->studentsCount();
                $taughtHours = $stats->taughtHoursCount();
                $soldHours = $stats->soldHoursCount();
                $acquisitionRate = $period->acquisition_rate;
                $newStudents = $period->newStudents()->count();

                if ($enrollments === 0 && $students === 0) {
                    continue;
                }

                $periodRows[] = [
                    'name' => $period->name,
                    'enrollments' => $enrollments,
                    'students' => $students,
                    'acquisitionRate' => $acquisitionRate.'%',
                    'newStudents' => $newStudents,
                    'taughtHours' => $taughtHours,
                    'soldHours' => $soldHours,
                    'isYearSummary' => false,
                ];

                $chartLabels[] = $period->name;
                $chartStudents[] = $students;
                $chartEnrollments[] = $enrollments;

                $yearEnrollments += $enrollments;
                $yearTaughtHours += $taughtHours;
                $yearSoldHours += $soldHours;
            }

            if (count($periodRows) === 0) {
                continue;
            }

            // Calculate unique students for the whole year
            $yearStats = new StatService(external: false, partner: null, reference: $year);
            $yearStudents = $yearStats->studentsCount();

            foreach ($periodRows as $row) {
                $rows[] = $row;
            }

            $rows[] = [
                'name' => $year->name,
                'enrollments' => $yearEnrollments,
                'students' => $yearStudents,
                'acquisitionRate' => '-',
                'newStudents' => '-',
                'taughtHours' => $yearTaughtHours,
                'soldHours' => $yearSoldHours,
                'isYearSummary' => true,
            ];
        }

        $this->reportData = $rows;
        $this->chartData = [
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'label' => __('Students'),
                    'data' => $chartStudents,
                    'backgroundColor' => '#98d1f1',
                    'borderColor' => '#5b76d8',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => __('Enrollments'),
                    'data' => $chartEnrollments,
                    'borderColor' => '#dd4b39',
                    'backgroundColor' => '#ffc9d1',
                    'fill' => true,
                    'tension' => 0.3,
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
        return __('Internal Report');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Internal Report');
    }
}
