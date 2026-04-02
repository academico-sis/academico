<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\Course;
use App\Models\Enrollment;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class CourseAttendance extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()?->can('attendance.view') ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.course-attendance';

    protected static bool $shouldRegisterNavigation = false;

    public ?int $courseId = null;

    public string $courseName = '';

    /** @var array<int, array<string, mixed>> */
    public array $events = [];

    /** @var array<int, array<string, mixed>> */
    public array $students = [];

    /** @var array<int, array<string, mixed>> */
    public array $attendanceTypes = [];

    public function mount(): void
    {
        $this->courseId = request()->integer('courseId') ?: null;

        if (! $this->courseId) {
            return;
        }

        $course = Course::find($this->courseId);

        if (! $course) {
            return;
        }

        abort_unless(Gate::allows('view-course-attendance', $course), 403);

        $this->courseName = $course->name;

        $this->attendanceTypes = AttendanceType::all()
            ->map(fn ($type) => [
                'id' => (int) $type->id,
                'name' => $type->name,
                'color' => $type->class ?? 'gray',
            ])
            ->toArray();

        $this->loadData();
    }

    protected function loadData(): void
    {
        $course = Course::with(['events' => fn ($q) => $q->orderBy('start'), 'enrollments.student'])
            ->find($this->courseId);

        if (! $course) {
            return;
        }

        $this->events = $course->events
            ->map(fn ($event) => [
                'id' => (int) $event->id,
                'date' => $event->start ? Carbon::parse($event->start)->format('d/m') : '',
            ])
            ->toArray();

        $eventIds = collect($this->events)->pluck('id')->toArray();

        $enrollments = Enrollment::with(['student'])
            ->where('course_id', $this->courseId)
            ->get();

        $allAttendance = Attendance::whereIn('event_id', $eventIds)
            ->whereIn('student_id', $enrollments->pluck('student_id'))
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($a) => $a->student_id.'-'.$a->event_id)
            ->map->first();

        $studentsData = [];
        foreach ($enrollments as $enrollment) {
            $eventAttendances = [];
            foreach ($eventIds as $eventId) {
                $key = $enrollment->student_id.'-'.$eventId;
                $att = $allAttendance->get($key);
                $eventAttendances[$eventId] = $att ? (int) $att->attendance_type_id : null;
            }

            $studentsData[] = [
                'studentId' => (int) $enrollment->student_id,
                'studentName' => $enrollment->student?->name ?? '',
                'attendances' => $eventAttendances,
            ];
        }

        $this->students = collect($studentsData)->sortBy('studentName')->values()->toArray();
    }

    public function toggleAttendance(int $studentId, int $eventId, int $typeId): void
    {
        $event = \App\Models\Event::find($eventId);
        abort_unless($event && Gate::allows('edit-attendance', $event), 403);

        Attendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'event_id' => $eventId,
            ],
            [
                'attendance_type_id' => $typeId,
            ],
        );

        foreach ($this->students as $sIndex => $student) {
            if ($student['studentId'] === $studentId) {
                $this->students[$sIndex]['attendances'][$eventId] = $typeId;

                break;
            }
        }

        Notification::make()
            ->success()
            ->title(__('Attendance updated'))
            ->duration(1500)
            ->send();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Attendance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Course Attendance');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->courseName
            ? __('Attendance').': '.$this->courseName
            : __('Course Attendance');
    }
}
