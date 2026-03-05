<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Event;
use App\Models\Period;
use App\Models\Teacher;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;


class AttendanceMonitor extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 220;

    protected string $view = 'filament.pages.attendance-monitor';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('reports.view') ?? false;
    }

    public ?int $selectedPeriodId = null;

    public ?int $selectedTeacherId = null;

    public int $absencesPage = 1;

    public int $coursesPage = 1;

    protected int $perPage = 15;

    /** @var array<int, array<string, mixed>> */
    public array $absencesPerStudent = [];

    /** @var array<int, array<string, mixed>> */
    public array $coursesData = [];

    public function mount(): void
    {
        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;
        $this->loadData();
    }

    public function updatedSelectedPeriodId(): void
    {
        $this->absencesPage = 1;
        $this->coursesPage = 1;
        $this->loadData();
    }

    public function updatedSelectedTeacherId(): void
    {
        $this->absencesPage = 1;
        $this->coursesPage = 1;
        $this->loadData();
    }

    public function goToAbsencesPage(int $page): void
    {
        $this->absencesPage = $page;
    }

    public function goToCoursesPage(int $page): void
    {
        $this->coursesPage = $page;
    }

    protected function loadData(): void
    {
        if (! $this->selectedPeriodId) {
            return;
        }

        $coursesQuery = Course::where('period_id', $this->selectedPeriodId);

        if ($this->selectedTeacherId) {
            $coursesQuery->where('teacher_id', $this->selectedTeacherId);
        }

        $coursesIds = $coursesQuery->pluck('id');

        $eventsIds = Event::whereIn('course_id', $coursesIds)->pluck('id');

        $this->absencesPerStudent = Attendance::with(['student', 'event', 'event.course'])
            ->whereIn('event_id', $eventsIds)
            ->whereIn('attendance_type_id', [3, 4])
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($att) => $att->student_id.'-'.$att->event_id)
            ->map->first()
            ->groupBy('student_id')
            ->map(fn ($items) => [
                'studentName' => $items->first()->student->name ?? '',
                'absencesCount' => $items->count(),
                'courseName' => $items->first()->event->course?->name ?? '',
                'studentId' => $items->first()->student_id,
                'courseId' => $items->first()->event->course?->id,
            ])
            ->sortByDesc('absencesCount')
            ->values()
            ->toArray();

        $coursesListQuery = Course::with(['events', 'enrollments', 'attendance'])
            ->where('period_id', $this->selectedPeriodId)
            ->whereHas('events')
            ->whereHas('enrollments');

        if ($this->selectedTeacherId) {
            $coursesListQuery->where('teacher_id', $this->selectedTeacherId);
        }

        $courses = $coursesListQuery->get();

        $coursesdata = [];
        foreach ($courses as $course) {
            $eventsWithMissingAttendanceCount = 0;

            $courseAttendanceRecords = $course->attendance;
            $courseEnrollments = $course->enrollments;

            $coursePastEvents = $course->events->filter(
                fn ($event) => Carbon::parse($event->start)->lt(Carbon::now())
            )->sortByDesc('start');

            foreach ($coursePastEvents as $event) {
                $eventAttendance = $courseAttendanceRecords->where('event_id', $event->id);
                foreach ($courseEnrollments as $enrollment) {
                    if ($eventAttendance->where('student_id', $enrollment->student_id)->isEmpty()) {
                        $eventsWithMissingAttendanceCount++;

                        break;
                    }
                }
            }

            $coursesdata[] = [
                'id' => $course->id,
                'name' => $course->name,
                'teacherName' => $course->course_teacher_name,
                'exemptAttendance' => (bool) $course->exempt_attendance,
                'missing' => $eventsWithMissingAttendanceCount,
            ];
        }

        $this->coursesData = collect($coursesdata)->sortByDesc('missing')->values()->toArray();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Administration');
    }

    public static function getNavigationLabel(): string
    {
        return __('Attendance Monitor');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Attendance Monitor');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $absencesCollection = collect($this->absencesPerStudent);
        $coursesCollection = collect($this->coursesData);

        return [
            'teachers' => Teacher::with('user')->get()->sortBy('name'),
            'paginatedAbsences' => $absencesCollection->forPage($this->absencesPage, $this->perPage)->values(),
            'absencesTotalPages' => (int) ceil($absencesCollection->count() / $this->perPage),
            'paginatedCourses' => $coursesCollection->forPage($this->coursesPage, $this->perPage)->values(),
            'coursesTotalPages' => (int) ceil($coursesCollection->count() / $this->perPage),
        ];
    }
}
