<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\Event;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class EventAttendance extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()?->can('attendance.view') ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.event-attendance';

    protected static bool $shouldRegisterNavigation = false;

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

        Attendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'event_id' => $this->eventId,
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

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->event
            ? __('Attendance').': '.($this->event->name ?? ($this->event->start ? Carbon::parse($this->event->start)->format('d/m/Y') : ''))
            : __('Event Attendance');
    }
}
