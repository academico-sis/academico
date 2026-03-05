<?php

namespace App\Filament\Pages;

use App\Models\AttendanceType;
use App\Models\Course;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use BackedEnum;
use Filament\Pages\Page;

class AttendanceByLevelAndRhythmReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bars-3';

    protected static ?int $navigationSort = 814;

    public static function canAccess(): bool
    {
        return (auth()->user()?->can('reports.view') ?? false)
            && (bool) config('settings.attendance_reports_enabled');
    }

    protected string $view = 'filament.pages.attendance-by-level-and-rhythm-report';

    public ?int $selectedPeriodId = null;

    /** @var array<string, mixed> */
    public array $levelChartData = [];

    /** @var array<int, array<string, mixed>> */
    public array $levelTableData = [];

    /** @var array<string, mixed> */
    public array $rhythmChartData = [];

    /** @var array<int, array<string, mixed>> */
    public array $rhythmTableData = [];

    /** @var array<int, array<string, mixed>> */
    public array $attendanceTypes = [];

    public function mount(): void
    {
        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;

        $this->attendanceTypes = AttendanceType::all()
            ->map(fn ($t) => ['id' => $t->id, 'name' => $t->name, 'color' => $t->color ?? '#6b7280'])
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
            ->has('attendance')
            ->has('events')
            ->with(['attendance', 'level', 'rhythm'])
            ->get();

        $this->loadLevelData($courses);
        $this->loadRhythmData($courses);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Course>  $courses
     */
    protected function loadLevelData($courses): void
    {
        $groups = $courses->groupBy('level_id')
            ->filter(fn ($g, $k) => Level::find($k) !== null);

        $labels = [];
        $datasets = [];
        $tableRows = [];

        foreach ($this->attendanceTypes as $type) {
            $datasets[$type['id']] = [
                'label' => $type['name'],
                'data' => [],
                'backgroundColor' => $type['color'],
            ];
        }

        foreach ($groups as $levelId => $groupCourses) {
            $level = Level::find($levelId);
            $labels[] = $level->name;

            $totalAttendance = 0;
            $countsByType = [];

            foreach ($this->attendanceTypes as $type) {
                $countsByType[$type['id']] = 0;
            }

            foreach ($groupCourses as $course) {
                $totalAttendance += $course->attendance->count();
                foreach ($this->attendanceTypes as $type) {
                    $countsByType[$type['id']] += $course->attendance->where('attendance_type_id', $type['id'])->count();
                }
            }

            $row = ['label' => $level->name, 'total' => $totalAttendance];

            foreach ($this->attendanceTypes as $type) {
                $pct = $totalAttendance > 0 ? round(100 * $countsByType[$type['id']] / $totalAttendance) : 0;
                $datasets[$type['id']]['data'][] = $pct;
                $row['type_'.$type['id']] = $pct.'%';
            }

            $tableRows[] = $row;
        }

        $this->levelTableData = $tableRows;
        $this->levelChartData = [
            'labels' => $labels,
            'datasets' => array_values($datasets),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Course>  $courses
     */
    protected function loadRhythmData($courses): void
    {
        $groups = $courses->groupBy('rhythm_id')
            ->filter(fn ($g, $k) => Rhythm::find($k) !== null);

        $labels = [];
        $datasets = [];
        $tableRows = [];

        foreach ($this->attendanceTypes as $type) {
            $datasets[$type['id']] = [
                'label' => $type['name'],
                'data' => [],
                'backgroundColor' => $type['color'],
            ];
        }

        foreach ($groups as $rhythmId => $groupCourses) {
            $rhythm = Rhythm::find($rhythmId);
            $labels[] = $rhythm->name;

            $totalAttendance = 0;
            $countsByType = [];

            foreach ($this->attendanceTypes as $type) {
                $countsByType[$type['id']] = 0;
            }

            foreach ($groupCourses as $course) {
                $totalAttendance += $course->attendance->count();
                foreach ($this->attendanceTypes as $type) {
                    $countsByType[$type['id']] += $course->attendance->where('attendance_type_id', $type['id'])->count();
                }
            }

            $row = ['label' => $rhythm->name, 'total' => $totalAttendance];

            foreach ($this->attendanceTypes as $type) {
                $pct = $totalAttendance > 0 ? round(100 * $countsByType[$type['id']] / $totalAttendance) : 0;
                $datasets[$type['id']]['data'][] = $pct;
                $row['type_'.$type['id']] = $pct.'%';
            }

            $tableRows[] = $row;
        }

        $this->rhythmTableData = $tableRows;
        $this->rhythmChartData = [
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
        return __('Attendance by Level & Rhythm');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Attendance by Level & Rhythm');
    }
}
