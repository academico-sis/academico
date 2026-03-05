<?php

namespace App\Filament\Pages;

use App\Models\Config;
use App\Models\Partner;
use App\Models\Period;
use App\Models\Year;
use App\Services\StatService;
use BackedEnum;
use Filament\Pages\Page;

class ExternalReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 830;

    public static function canAccess(): bool
    {
        return (auth()->user()?->can('reports.view') ?? false)
            && (bool) config('settings.external_courses_enabled');
    }

    protected string $view = 'filament.pages.external-report';

    public ?int $startFromPeriodId = null;

    public ?int $selectedPartnerId = null;

    /** @var array<int, array<string, mixed>> */
    public array $allPeriods = [];

    /** @var array<int, array<string, mixed>> */
    public array $partners = [];

    /** @var array<int, array<string, mixed>> */
    public array $reportData = [];

    /** @var array<string, mixed> */
    public array $chartData = [];

    /** @var array<string, mixed> */
    public array $yearChartData = [];

    public function mount(): void
    {
        $this->allPeriods = Period::withoutGlobalScopes()
            ->orderBy('year_id')
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])
            ->toArray();

        $this->partners = Partner::orderBy('name')
            ->get()
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])
            ->toArray();

        $configFirstPeriod = Config::where('name', 'first_external_period')->first()
            ?? Config::where('name', 'first_period')->first();
        $this->startFromPeriodId = $configFirstPeriod?->value
            ?? ($this->allPeriods[0]['id'] ?? null);

        $this->loadData();
    }

    public function updatedStartFromPeriodId(): void
    {
        $this->loadData();
    }

    public function updatedSelectedPartnerId(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        if (! $this->startFromPeriodId) {
            return;
        }

        $selectedPartner = $this->selectedPartnerId ? Partner::find($this->selectedPartnerId) : null;

        $periods = Period::withoutGlobalScopes()
            ->where('id', '>=', $this->startFromPeriodId)
            ->orderBy('year_id')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $data = [];
        $yearData = [];
        $periodLabels = [];
        $periodStudents = [];
        $periodEnrollments = [];
        $yearLabels = [];
        $yearStudents = [];
        $yearEnrollments = [];

        $currentYearId = null;

        foreach ($periods as $period) {
            $stats = new StatService(external: true, reference: $period, partner: $selectedPartner);

            $courses = $stats->coursesCount();
            $partnerships = $stats->partnershipsCount();
            $enrollments = $stats->enrollmentsCount();
            $students = $stats->studentsCount();
            $taughtHours = $stats->taughtHoursCount();
            $soldHours = $stats->soldHoursCount();

            if ($enrollments === 0 && $students === 0 && $courses === 0) {
                continue;
            }

            // Year boundary handling
            if ($currentYearId !== $period->year_id) {
                $currentYearId = $period->year_id;
                $yearData[$currentYearId] = [
                    'year_name' => Year::find($currentYearId)?->name ?? '',
                    'students' => 0,
                    'enrollments' => 0,
                    'taught_hours' => 0,
                    'sold_hours' => 0,
                    'courses' => 0,
                    'partnerships' => 0,
                ];
            }

            $data[] = [
                'name' => $period->name,
                'year_id' => $period->year_id,
                'courses' => $courses,
                'partnerships' => $partnerships,
                'enrollments' => $enrollments,
                'students' => $students,
                'taught_hours' => $taughtHours,
                'sold_hours' => $soldHours,
                'isYearSummary' => false,
            ];

            $periodLabels[] = $period->name;
            $periodStudents[] = $students;
            $periodEnrollments[] = $enrollments;

            $yearData[$currentYearId]['students'] += $students;
            $yearData[$currentYearId]['enrollments'] += $enrollments;
            $yearData[$currentYearId]['taught_hours'] += $taughtHours;
            $yearData[$currentYearId]['sold_hours'] += $soldHours;
            $yearData[$currentYearId]['courses'] += $courses;
        }

        // Build flat report data with year summary rows
        $reportData = [];
        $groupedByYear = collect($data)->groupBy('year_id');

        foreach ($groupedByYear as $yearId => $yearPeriods) {
            foreach ($yearPeriods as $row) {
                $reportData[] = $row;
            }

            if (isset($yearData[$yearId])) {
                $yd = $yearData[$yearId];
                $reportData[] = [
                    'name' => $yd['year_name'].' '.__('Total'),
                    'year_id' => $yearId,
                    'courses' => $yd['courses'],
                    'partnerships' => $yearData[$yearId]['partnerships'] ?? 0,
                    'enrollments' => $yd['enrollments'],
                    'students' => $yd['students'],
                    'taught_hours' => $yd['taught_hours'],
                    'sold_hours' => $yd['sold_hours'],
                    'isYearSummary' => true,
                ];

                $yearLabels[] = $yd['year_name'];
                $yearStudents[] = $yd['students'];
                $yearEnrollments[] = $yd['enrollments'];
            }
        }

        $this->reportData = $reportData;

        $this->chartData = [
            'labels' => $periodLabels,
            'datasets' => [
                [
                    'label' => __('Students'),
                    'data' => $periodStudents,
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => __('Enrollments'),
                    'data' => $periodEnrollments,
                    'backgroundColor' => '#ef4444',
                ],
            ],
        ];

        $this->yearChartData = [
            'labels' => $yearLabels,
            'datasets' => [
                [
                    'label' => __('Students'),
                    'data' => $yearStudents,
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => __('Enrollments'),
                    'data' => $yearEnrollments,
                    'backgroundColor' => '#ef4444',
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
        return __('External Report');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('External Report');
    }
}
