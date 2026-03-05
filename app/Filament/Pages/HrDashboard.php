<?php

namespace App\Filament\Pages;

use App\Models\Period;
use App\Models\Teacher;
use BackedEnum;
use Filament\Pages\Page;

class HrDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 520;

    protected string $view = 'filament.pages.hr-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('hr.view') ?? false;
    }

    public ?int $selectedPeriodId = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    /** @var array<int, array<string, mixed>> */
    public array $teacherHours = [];

    public function mount(): void
    {
        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;

        if ($period) {
            $this->startDate = $period->start ?? now()->startOfMonth()->format('Y-m-d');
            $this->endDate = $period->end ?? now()->endOfMonth()->format('Y-m-d');
        }

        $this->loadData();
    }

    public function updatedSelectedPeriodId(): void
    {
        $period = Period::find($this->selectedPeriodId);

        if ($period) {
            $this->startDate = $period->start;
            $this->endDate = $period->end;
        }

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

        $teachers = Teacher::with('user')
            ->get()
            ->sortBy(fn ($t) => $t->user?->name);

        $data = [];

        foreach ($teachers as $teacher) {
            $plannedHours = $teacher->plannedHoursInPeriod($this->startDate, $this->endDate);
            $remoteHours = $teacher->plannedRemoteHoursInPeriod($this->startDate, $this->endDate);

            $leaveDays = $teacher->leaves()
                ->where('date', '>=', $this->startDate)
                ->where('date', '<=', $this->endDate)
                ->count();

            $data[] = [
                'teacherName' => $teacher->user?->name ?? 'Teacher #'.$teacher->id,
                'teacherId' => $teacher->id,
                'plannedHours' => round($plannedHours, 1),
                'remoteHours' => round($remoteHours, 1),
                'totalHours' => round($plannedHours + $remoteHours, 1),
                'leaveDays' => $leaveDays,
            ];
        }

        $this->teacherHours = $data;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Organization');
    }

    public static function getNavigationLabel(): string
    {
        return __('HR Dashboard');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('HR Dashboard');
    }
}
