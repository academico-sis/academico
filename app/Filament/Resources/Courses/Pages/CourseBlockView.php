<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Course;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Teacher;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class CourseBlockView extends Page
{
    protected static string $resource = CourseResource::class;

    protected string $view = 'filament.resources.courses.pages.course-block-view';

    public ?int $selectedPeriodId = null;

    public ?int $selectedTeacherId = null;

    /** @var array<int> */
    public array $selectedRhythmIds = [];

    /** @var array<int> */
    public array $selectedLevelIds = [];

    /** @var array<int, array<string, mixed>> */
    public array $courses = [];

    public function mount(): void
    {
        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;
        $this->loadCourses();
    }

    public function updatedSelectedPeriodId(): void
    {
        $this->loadCourses();
    }

    public function updatedSelectedTeacherId(): void
    {
        $this->loadCourses();
    }

    public function updatedSelectedRhythmIds(): void
    {
        $this->loadCourses();
    }

    public function updatedSelectedLevelIds(): void
    {
        $this->loadCourses();
    }

    public function loadCourses(): void
    {
        $query = Course::query()
            ->with(['teacher', 'room', 'rhythm', 'level', 'times']);

        if ($this->selectedPeriodId) {
            $query->where('period_id', $this->selectedPeriodId);
        }

        if ($this->selectedTeacherId) {
            $query->where('teacher_id', $this->selectedTeacherId);
        }

        if (! empty($this->selectedRhythmIds)) {
            $query->whereIn('rhythm_id', $this->selectedRhythmIds);
        }

        if (! empty($this->selectedLevelIds)) {
            $query->whereIn('level_id', $this->selectedLevelIds);
        }

        $this->courses = $query
            ->orderBy('start_date')
            ->get()
            ->map(fn (Course $course) => [
                'id' => $course->id,
                'name' => $course->name,
                'teacher' => $course->course_teacher_name,
                'room' => $course->room?->name,
                'schedule' => $course->course_times,
                'start_date' => $course->start_date?->format('d/m/Y'),
                'end_date' => $course->end_date?->format('d/m/Y'),
                'volume' => $course->volume,
                'enrollments_count' => $course->course_enrollments_count,
                'spots' => $course->spots,
                'has_teacher' => $course->teacher_id !== null,
                'has_room' => $course->room_id !== null,
                'edit_url' => CourseResource::canEdit($course) ? CourseResource::getUrl('edit', ['record' => $course->id]) : null,
                'enrollments_url' => CourseResource::getUrl('enrollments', ['record' => $course->id]),
            ])
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('list_view')
                ->label(__('Switch to list view'))
                ->icon('heroicon-o-list-bullet')
                ->url(CourseResource::getUrl('index')),
        ];
    }

    public function getTitle(): string
    {
        return __('Courses');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'periods' => Period::orderByDesc('id')->get(),
            'teachers' => Teacher::join('users', 'teachers.id', '=', 'users.id')
                ->orderBy('users.lastname')
                ->orderBy('users.firstname')
                ->select('teachers.*')
                ->get(),
            'rhythms' => Rhythm::orderBy('name')->get(),
            'levels' => Level::orderBy('name')->get(),
        ];
    }
}
