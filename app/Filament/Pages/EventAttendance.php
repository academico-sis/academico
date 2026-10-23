<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\Enrollment;
use App\Models\Event;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;

class EventAttendance extends Page
{
    /**
     * Teachers can open the page; the gates restrict them to their own events.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->can('attendance.view') || $user?->isTeacher();
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.event-attendance';

    protected static bool $shouldRegisterNavigation = false;

    #[Locked]
    public ?int $eventId = null;

    public ?Event $event = null;

    /** @var array<int, array<string, mixed>> */
    public array $attendanceTypes = [];

    /** @var array<int, array<string, mixed>> */
    public array $students = [];

    public function mount(): void
    {
        $this->eventId = request()->integer('eventId') ?: null;

        if ($this->eventId) {
            $event = Event::find($this->eventId);
            abort_unless($event && Gate::allows('view-event-attendance', $event), 403);
            $this->loadData();
        }
    }

    protected function loadData(): void
    {
        $this->event = Event::with(['course.enrollments.student'])->find($this->eventId);

        if (! $this->event) {
            return;
        }

        $this->attendanceTypes = AttendanceType::all()
            ->map(fn ($type) => [
                'id' => (int) $type->id,
                'name' => $type->name,
                'color' => $type->class ?? 'gray',
            ])
            ->toArray();

        $existingAttendance = Attendance::where('event_id', $this->eventId)
            ->orderBy('id')
            ->get()
            ->unique('student_id')
            ->keyBy('student_id');

        $enrollments = $this->event->course?->enrollments ?? collect();

        $studentsData = [];
        foreach ($enrollments as $enrollment) {
            $att = $existingAttendance->get($enrollment->student_id);
            $studentsData[] = [
                'studentId' => $enrollment->student_id,
                'studentName' => $enrollment->student?->name ?? '',
                'currentTypeId' => $att ? (int) $att->attendance_type_id : null,
            ];
        }

        $this->students = collect($studentsData)->sortBy('studentName')->values()->toArray();
    }

    public function toggleAttendance(int $studentId, int $attendanceTypeId): void
    {
        if (! $this->eventId) {
            return;
        }

        abort_unless($this->event && Gate::allows('edit-attendance', $this->event), 403);
        abort_unless(Enrollment::where('course_id', $this->event->course_id)->where('student_id', $studentId)->exists(), 403);

        Attendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'event_id' => $this->event->id,
            ],
            [
                'attendance_type_id' => $attendanceTypeId,
            ],
        );

        foreach ($this->students as $index => $student) {
            if ($student['studentId'] === $studentId) {
                $this->students[$index]['currentTypeId'] = $attendanceTypeId;

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
        return __('Event Attendance');
    }

    public function getTitle(): string|Htmlable
    {
        return $this->event
            ? __('Attendance').': '.($this->event->name ?? ($this->event->start ? Carbon::parse($this->event->start)->format('d/m/Y') : ''))
            : __('Event Attendance');
    }
}
