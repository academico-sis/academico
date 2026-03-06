<?php

namespace App\Filament\Pages;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Period;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class GradeEdit extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->can('evaluation.edit') || $user?->isTeacher();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    protected string $view = 'filament.pages.grade-edit';

    public ?int $selectedPeriodId = null;

    public ?int $selectedCourseId = null;

    /** @var array<int, array<string, mixed>> */
    public array $courses = [];

    /** @var array<int, array<string, mixed>> */
    public array $gradeTypes = [];

    /** @var array<int, array<string, mixed>> */
    public array $enrollments = [];

    public function mount(): void
    {
        $courseId = request()->integer('courseId') ?: null;

        if ($courseId) {
            $course = Course::find($courseId);
            if ($course && Gate::allows('edit-course-grades', $course)) {
                $this->selectedPeriodId = $course->period_id;
                $this->loadCourses();
                $this->selectedCourseId = $course->id;
                $this->loadGradeData();

                return;
            }
        }

        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;
        $this->loadCourses();
    }

    public function updatedSelectedPeriodId(): void
    {
        $this->selectedCourseId = null;
        $this->gradeTypes = [];
        $this->enrollments = [];
        $this->loadCourses();
    }

    public function updatedSelectedCourseId(): void
    {
        $this->loadGradeData();
    }

    protected function loadCourses(): void
    {
        if (! $this->selectedPeriodId) {
            return;
        }

        $this->courses = Course::where('period_id', $this->selectedPeriodId)
            ->whereHas('enrollments')
            ->get()
            ->filter(fn ($course) => Gate::allows('edit-course-grades', $course))
            ->sortBy('name')
            ->map(fn ($course) => [
                'id' => $course->id,
                'name' => $course->name,
            ])
            ->values()
            ->toArray();
    }

    protected function loadGradeData(): void
    {
        if (! $this->selectedCourseId) {
            return;
        }

        $course = Course::with('evaluationType.gradeTypes')->find($this->selectedCourseId);

        if (! $course || ! $course->evaluationType || Gate::denies('edit-course-grades', $course)) {
            $this->gradeTypes = [];
            $this->enrollments = [];

            return;
        }

        $this->gradeTypes = $course->evaluationType->gradeTypes
            ->map(fn ($gt) => [
                'id' => $gt->id,
                'name' => $gt->name,
                'total' => $gt->total ?? 100,
            ])
            ->toArray();

        $gradeTypeIds = collect($this->gradeTypes)->pluck('id')->toArray();

        $enrollments = Enrollment::with(['student', 'grades'])
            ->where('course_id', $this->selectedCourseId)
            ->whereDoesntHave('childrenEnrollments')
            ->get();

        $this->enrollments = $enrollments->map(function ($enrollment) use ($gradeTypeIds) {
            $grades = [];
            foreach ($gradeTypeIds as $gtId) {
                $grade = $enrollment->grades->where('grade_type_id', $gtId)->first();
                $grades[$gtId] = $grade?->grade ?? '';
            }

            return [
                'enrollmentId' => $enrollment->id,
                'studentName' => $enrollment->student?->name ?? '',
                'studentId' => $enrollment->student_id,
                'grades' => $grades,
            ];
        })
            ->sortBy('studentName')
            ->values()
            ->toArray();
    }

    public function saveGrade(int $enrollmentId, int $gradeTypeId, string $value): void
    {
        $course = Course::find($this->selectedCourseId);

        if (! $course || Gate::denies('edit-course-grades', $course)) {
            abort(403);
        }

        $numericValue = $value !== '' ? (float) $value : null;

        if ($numericValue === null) {
            Grade::where('enrollment_id', $enrollmentId)
                ->where('grade_type_id', $gradeTypeId)
                ->delete();
        } else {
            Grade::updateOrCreate(
                [
                    'enrollment_id' => $enrollmentId,
                    'grade_type_id' => $gradeTypeId,
                ],
                [
                    'grade' => $numericValue,
                ],
            );
        }

        foreach ($this->enrollments as $index => $enrollment) {
            if ($enrollment['enrollmentId'] === $enrollmentId) {
                $this->enrollments[$index]['grades'][$gradeTypeId] = $value;

                break;
            }
        }

        Notification::make()
            ->success()
            ->title(__('Grade saved'))
            ->duration(1500)
            ->send();
    }

    public static function getNavigationGroup(): ?string
    {
        return 'A revoir / WIP';
    }

    public static function getNavigationLabel(): string
    {
        return __('Grades');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Grade Editing');
    }
}
