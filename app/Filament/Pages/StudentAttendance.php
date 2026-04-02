<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\Course;
use App\Models\Student;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class StudentAttendance extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()?->can('attendance.view') ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.student-attendance';

    public ?int $studentId = null;

    public ?int $courseId = null;

    public string $studentName = '';

    /** @var array<int, array<string, mixed>> */
    public array $enrollments = [];

    /** @var array<int, array<string, mixed>> */
    public array $events = [];

    /** @var array<int, array<string, mixed>> */
    public array $attendanceTypes = [];

    public ?int $attendanceRatio = null;

    public int $absenceCount = 0;

    public function mount(): void
    {
        $this->studentId = request()->integer('studentId') ?: null;
        $this->courseId = request()->integer('courseId') ?: null;

        if (! $this->studentId) {
            return;
        }

        $student = Student::findOrFail($this->studentId);
        $this->studentName = $student->name;

        $this->attendanceTypes = AttendanceType::all()
            ->map(fn ($type) => [
                'id' => (int) $type->id,
                'name' => $type->name,
                'color' => $type->class ?? 'gray',
            ])
            ->toArray();

        $studentEnrollments = $student->enrollments()->with('course.period')->get();

        $this->enrollments = $studentEnrollments->map(fn ($enrollment) => [
            'courseId' => $enrollment->course_id,
            'label' => ($enrollment->course?->period?->name ?? '').' → '.($enrollment->course?->name ?? ''),
        ])->toArray();

        if (! $this->courseId && $studentEnrollments->isNotEmpty()) {
            $this->courseId = $studentEnrollments->first()->course_id;
        }

        $this->loadData();
    }

    public function updatedCourseId(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        if (! $this->courseId) {
            $this->events = [];
            $this->attendanceRatio = null;
            $this->absenceCount = 0;

            return;
        }

        $course = Course::find($this->courseId);

        if (! $course) {
            return;
        }

        $courseEvents = $course->eventsWithExpectedAttendance()
            ->orderBy('start', 'desc')
            ->get();

        $existingAttendance = Attendance::where('student_id', $this->studentId)
            ->whereIn('event_id', $courseEvents->pluck('id'))
            ->orderBy('id')
            ->get()
            ->unique('event_id')
            ->keyBy('event_id');

        $eventsData = [];
        foreach ($courseEvents as $event) {
            $att = $existingAttendance->get($event->id);
            $eventsData[] = [
                'id' => $event->id,
                'name' => $event->name ?? '',
                'date' => Carbon::parse($event->start)->format('d/m/Y'),
                'currentTypeId' => $att ? (int) $att->attendance_type_id : null,
            ];
        }

        $this->events = $eventsData;

        $this->computeStats();
    }

    protected function computeStats(): void
    {
        $total = 0;
        $presentScore = 0;
        $absences = 0;

        foreach ($this->events as $event) {
            if ($event['currentTypeId'] !== null) {
                $total++;
                if ($event['currentTypeId'] === 1) {
                    $presentScore++;
                } elseif ($event['currentTypeId'] === 2) {
                    $presentScore += 0.75;
                } elseif (in_array($event['currentTypeId'], [3, 4])) {
                    $absences++;
                }
            }
        }

        $this->attendanceRatio = $total > 0 ? (int) round(100 * $presentScore / $total) : null;
        $this->absenceCount = $absences;
    }

    public function toggleAttendance(int $eventId, int $typeId): void
    {
        if (! $this->studentId) {
            return;
        }

        $event = \App\Models\Event::find($eventId);
        abort_unless($event && Gate::allows('edit-attendance', $event), 403);

        Attendance::updateOrCreate(
            [
                'student_id' => $this->studentId,
                'event_id' => $eventId,
            ],
            [
                'attendance_type_id' => $typeId,
            ],
        );

        foreach ($this->events as $index => $event) {
            if ($event['id'] === $eventId) {
                $this->events[$index]['currentTypeId'] = $typeId;

                break;
            }
        }

        $this->computeStats();

        Notification::make()
            ->success()
            ->title(__('Attendance updated'))
            ->duration(1500)
            ->send();
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Student Attendance Report');
    }
}
