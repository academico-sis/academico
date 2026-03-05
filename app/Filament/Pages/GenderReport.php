<?php

namespace App\Filament\Pages;

use App\Models\Config;
use App\Models\Period;
use App\Services\StatService;
use BackedEnum;
use Filament\Pages\Page;

class GenderReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 850;

    protected string $view = 'filament.pages.gender-report';

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
        $chartMale = [];
        $chartFemale = [];
        $chartUnknown = [];
        $rows = [];

        $groupedByYear = $periods->groupBy('year_id');

        foreach ($groupedByYear as $yearId => $yearPeriods) {
            $year = $yearPeriods->first()->year;
            $periodRows = [];

            foreach ($yearPeriods as $period) {
                $stats = new StatService(external: false, partner: null, reference: $period);
                $total = $stats->studentsCount();

                if ($total === 0) {
                    continue;
                }

                $male = $stats->studentsCount(2);
                $female = $stats->studentsCount(1);
                $unknown = $stats->studentsCount(0);

                $periodRows[] = [
                    'name' => $period->name,
                    'male' => round(100 * $male / $total, 1),
                    'female' => round(100 * $female / $total, 1),
                    'unknown' => round(100 * $unknown / $total, 1),
                    'isYearSummary' => false,
                ];

                $chartLabels[] = $period->name;
                $chartMale[] = round(100 * $male / $total, 1);
                $chartFemale[] = round(100 * $female / $total, 1);
                $chartUnknown[] = round(100 * $unknown / $total, 1);
            }

            if (count($periodRows) === 0) {
                continue;
            }

            foreach ($periodRows as $row) {
                $rows[] = $row;
            }

            // Year summary
            $yearStats = new StatService(external: false, partner: null, reference: $year);
            $yearTotal = $yearStats->studentsCount();

            $rows[] = [
                'name' => $year->name,
                'male' => $yearTotal > 0 ? round(100 * $yearStats->studentsCount(2) / $yearTotal, 1) : 0,
                'female' => $yearTotal > 0 ? round(100 * $yearStats->studentsCount(1) / $yearTotal, 1) : 0,
                'unknown' => $yearTotal > 0 ? round(100 * $yearStats->studentsCount(0) / $yearTotal, 1) : 0,
                'isYearSummary' => true,
            ];
        }

        $this->reportData = $rows;
        $this->chartData = [
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'label' => __('Male').' %',
                    'data' => $chartMale,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => __('Female').' %',
                    'data' => $chartFemale,
                    'borderColor' => '#ec4899',
                    'backgroundColor' => 'rgba(236, 72, 153, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => __('Unknown').' %',
                    'data' => $chartUnknown,
                    'borderColor' => '#9ca3af',
                    'backgroundColor' => 'rgba(156, 163, 175, 0.1)',
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
        return __('Gender Report');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Gender Report');
    }
}
