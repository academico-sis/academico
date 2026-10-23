<?php

namespace App\Filament\Pages;

use App\Filament\Actions\DownloadCertificateAction;
use App\Filament\Actions\DownloadCourseResultsAction;
use App\Filament\Actions\DownloadResultAction;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Period;
use App\Models\Teacher;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class TeacherDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.teacher-dashboard';

    public ?int $selectedPeriodId = null;

    /** @var array<int, array<string, mixed>> */
    public array $courses = [];

    /** @var array<int, array<string, mixed>> */
    public array $pendingAttendance = [];

    /** @var array<int, array<string, mixed>> */
    public array $results = [];

    public float $volume = 0;

    public float $remoteVolume = 0;

    public float $totalVolume = 0;

    /**
     * Records of the download links, loaded once per request so that each link is checked against its own record.
     *
     * @var array<string, Model|null>
     */
    protected array $linkRecords = [];

    public function mount(): void
    {
        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;

        if ($period) {
            $this->loadData($period);
        }
    }

    public function updatedSelectedPeriodId(): void
    {
        $period = Period::find($this->selectedPeriodId);

        if ($period) {
            $this->loadData($period);
        }
    }

    protected function loadData(Period $period): void
    {
        $teacher = $this->getTeacher();

        if (! $teacher) {
            return;
        }

        // Courses
        $this->courses = $teacher->period_courses($period)->map(function ($course) {
            return [
                'id' => $course->id,
                'name' => $course->name,
                'enrollments_count' => $course->enrollments_count,
                'start_date' => $course->start_date?->format('d/m/Y'),
                'end_date' => $course->end_date?->format('d/m/Y'),
                'room' => $course->room?->name ?? '-',
                'attendance_url' => CourseAttendance::getUrl(['courseId' => $course->id]),
                'grades_url' => $course->evaluationType?->gradeTypes()->exists() ? GradeEdit::getUrl(['courseId' => $course->id]) : null,
                'skills_url' => $course->evaluationType?->skills()->exists() ? SkillEvaluationPage::getUrl(['courseId' => $course->id]) : null,
            ];
        })->toArray();

        // Pending attendance
        $this->pendingAttendance = $teacher->events_with_pending_attendance($period)->map(function (Event $event) {
            return [
                'id' => $event->id,
                'name' => $event->name,
                'course_name' => $event->course?->name ?? '-',
                'start' => $event->start ? Carbon::parse($event->start)->format('d/m/Y H:i') : null,
                'url' => EventAttendance::getUrl(['eventId' => $event->id]),
            ];
        })->toArray();

        // Results
        $courseIds = $teacher->period_courses($period)->pluck('id');
        $this->results = Enrollment::with(['student', 'course', 'result.result_name'])
            ->whereIn('course_id', $courseIds)
            ->whereHas('result')
            ->get()
            ->map(fn (Enrollment $enrollment) => [
                'id' => $enrollment->id,
                'studentName' => $enrollment->student->name ?? '',
                'courseName' => $enrollment->course->name ?? '',
                'resultName' => $enrollment->result->result_name->name ?? '',
            ])
            ->toArray();

        // Volume
        $this->volume = $teacher->plannedHoursInPeriod($period->start, $period->end);
        $this->remoteVolume = $teacher->plannedRemoteHoursInPeriod($period->start, $period->end);
        $this->totalVolume = $this->volume + $this->remoteVolume;
    }

    public function downloadResultAction(): Action
    {
        return DownloadResultAction::make()
            ->record(fn (array $arguments): ?Model => $this->linkRecord(Enrollment::class, $arguments['enrollment'] ?? null))
            ->link()
            ->size('sm');
    }

    public function downloadCertificateAction(): Action
    {
        return DownloadCertificateAction::make()
            ->record(fn (array $arguments): ?Model => $this->linkRecord(Enrollment::class, $arguments['enrollment'] ?? null))
            ->link()
            ->size('sm');
    }

    public function downloadCourseResultsAction(): Action
    {
        return DownloadCourseResultsAction::make()
            ->record(fn (array $arguments): ?Model => $this->linkRecord(Course::class, $arguments['course'] ?? null))
            ->link()
            ->size('sm');
    }

    /**
     * @param  class-string<Model>  $model
     */
    protected function linkRecord(string $model, int|string|null $id): ?Model
    {
        return $this->linkRecords[$model.':'.$id] ??= $model::query()->find($id);
    }

    protected function getTeacher(): ?Teacher
    {
        return Teacher::where('id', auth()->id())->first();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isTeacher() ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isTeacher() ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('My Dashboard');
    }

    public function getTitle(): string
    {
        return __('Teacher Dashboard');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'periods' => Period::orderByDesc('year_id')->orderByDesc('order')->get(),
        ];
    }
}
