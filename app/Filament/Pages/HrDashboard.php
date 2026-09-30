<?php

namespace App\Filament\Pages;

use App\Models\Period;
use App\Models\Teacher;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

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

    public bool $usesDateFilter = false;

    /** @var array<int, array<string, mixed>> */
    public array $teacherHours = [];

    public function mount(): void
    {
        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;

        if ($period) {
            $this->startDate = $period->start ? Carbon::parse($period->start)->toDateString() : now()->startOfMonth()->toDateString();
            $this->endDate = $period->end ? Carbon::parse($period->end)->toDateString() : now()->endOfMonth()->toDateString();
        }

        $this->loadData();
    }

    public function updatedSelectedPeriodId(): void
    {
        $period = Period::find($this->selectedPeriodId);

        if ($period) {
            $this->startDate = $period->start ? Carbon::parse($period->start)->toDateString() : null;
            $this->endDate = $period->end ? Carbon::parse($period->end)->toDateString() : null;
        }

        $this->usesDateFilter = false;
        $this->loadData();
    }

    public function updatedStartDate(): void
    {
        $this->usesDateFilter = true;
        $this->loadData();
    }

    public function updatedEndDate(): void
    {
        $this->usesDateFilter = true;
        $this->loadData();
    }

    public function clearDateFilter(): void
    {
        $period = Period::find($this->selectedPeriodId);

        if ($period) {
            $this->startDate = $period->start ? Carbon::parse($period->start)->toDateString() : null;
            $this->endDate = $period->end ? Carbon::parse($period->end)->toDateString() : null;
        }

        $this->usesDateFilter = false;
        $this->loadData();
    }

    protected function loadData(): void
    {
        if (! $this->startDate || ! $this->endDate) {
            return;
        }

        $period = Period::find($this->selectedPeriodId);

        $teachers = Teacher::with('user')
            ->get()
            ->sortBy(fn ($t) => $t->user?->name);

        $data = [];

        foreach ($teachers as $teacher) {
            // Theoretical volumes from course definitions (legacy "Heures prévues")
            $theoreticalFaceToFace = $period
                ? (float) $teacher->courses()->realcourses()->where('period_id', $period->id)->sum('volume')
                : 0.0;
            $theoreticalRemote = $period
                ? (float) $teacher->courses()->realcourses()->where('period_id', $period->id)->sum('remote_volume')
                : 0.0;

            // Actual hours from scheduled events (legacy "Heures sur le calendrier")
            $scheduledFaceToFace = $teacher->plannedHoursInPeriod($this->startDate, $this->endDate);
            $scheduledRemote = $teacher->plannedRemoteHoursInPeriod($this->startDate, $this->endDate);

            $leaveDays = $teacher->leaves()
                ->where('date', '>=', $this->startDate)
                ->where('date', '<=', $this->endDate)
                ->count();

            $data[] = [
                'teacherName' => $teacher->user?->name ?? __('Teacher').' #'.$teacher->id,
                'teacherId' => $teacher->id,
                'theoreticalFaceToFace' => round($theoreticalFaceToFace, 2),
                'theoreticalRemote' => round($theoreticalRemote, 2),
                'theoreticalTotal' => round($theoreticalFaceToFace + $theoreticalRemote, 2),
                'scheduledFaceToFace' => round($scheduledFaceToFace, 2),
                'scheduledRemote' => round($scheduledRemote, 2),
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

    public function getTitle(): string|Htmlable
    {
        return __('HR Dashboard');
    }
}
