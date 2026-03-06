<?php

namespace App\Filament\Pages;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Period;
use App\Models\Result;
use App\Models\ResultType;
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

    /** @var array<int, array<string, mixed>> */
    public array $resultTypes = [];

    /** @var array<int, string> */
    public array $comments = [];

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

        $course = Course::with('evaluationType.gradeTypes.category')->find($this->selectedCourseId);

        if (! $course || ! $course->evaluationType || Gate::denies('edit-course-grades', $course)) {
            $this->gradeTypes = [];
            $this->enrollments = [];

            return;
        }

        $this->gradeTypes = $course->evaluationType->gradeTypes
            ->map(fn ($gt) => [
                'id' => $gt->id,
                'name' => $gt->name,
                'categoryName' => $gt->category?->name,
                'total' => $gt->total ?? 100,
            ])
            ->toArray();

        $this->resultTypes = ResultType::all()
            ->map(fn ($rt) => [
                'id' => $rt->id,
                'name' => $rt->name,
            ])
            ->toArray();

        $gradeTypeIds = collect($this->gradeTypes)->pluck('id')->toArray();

        $enrollments = Enrollment::with(['student', 'grades', 'result.result_name', 'result.comments'])
            ->where('course_id', $this->selectedCourseId)
            ->whereDoesntHave('childrenEnrollments')
            ->get();

        $this->comments = [];

        $this->enrollments = $enrollments->map(function ($enrollment) use ($gradeTypeIds) {
            $grades = [];
            foreach ($gradeTypeIds as $gtId) {
                $grade = $enrollment->grades->where('grade_type_id', $gtId)->first();
                $grades[$gtId] = $grade?->grade ?? '';
            }

            $total = $enrollment->grades->whereIn('grade_type_id', $gradeTypeIds)->sum('grade');

            $resultComment = $enrollment->result?->comments?->first();
            $this->comments[$enrollment->id] = $resultComment?->body ?? '';

            return [
                'enrollmentId' => $enrollment->id,
                'studentName' => $enrollment->student?->name ?? '',
                'studentId' => $enrollment->student_id,
                'grades' => $grades,
                'total' => $total,
                'resultTypeId' => $enrollment->result?->result_type_id,
                'resultTypeName' => $enrollment->result?->result_name?->name,
                'resultTypeColor' => $enrollment->result?->result_name?->color,
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

        $gradeTypeIds = collect($this->gradeTypes)->pluck('id')->toArray();

        foreach ($this->enrollments as $index => $enrollment) {
            if ($enrollment['enrollmentId'] === $enrollmentId) {
                $this->enrollments[$index]['grades'][$gradeTypeId] = $value;
                $this->enrollments[$index]['total'] = collect($this->enrollments[$index]['grades'])
                    ->filter(fn ($v) => $v !== '')
                    ->sum(fn ($v) => (float) $v);

                break;
            }
        }

        Notification::make()
            ->success()
            ->title(__('Grade saved'))
            ->duration(1500)
            ->send();
    }

    public function saveResult(int $enrollmentId, string $resultTypeId): void
    {
        $course = Course::find($this->selectedCourseId);

        if (! $course || Gate::denies('edit-course-grades', $course)) {
            abort(403);
        }

        if ($resultTypeId === '') {
            Result::where('enrollment_id', $enrollmentId)->delete();
        } else {
            Result::updateOrCreate(
                ['enrollment_id' => $enrollmentId],
                ['result_type_id' => (int) $resultTypeId],
            );
        }

        foreach ($this->enrollments as $index => $enrollment) {
            if ($enrollment['enrollmentId'] === $enrollmentId) {
                $resultType = $resultTypeId !== '' ? ResultType::find((int) $resultTypeId) : null;
                $this->enrollments[$index]['resultTypeId'] = $resultType?->id;
                $this->enrollments[$index]['resultTypeName'] = $resultType?->name;
                $this->enrollments[$index]['resultTypeColor'] = $resultType?->color;

                break;
            }
        }

        Notification::make()
            ->success()
            ->title(__('Result saved'))
            ->duration(1500)
            ->send();
    }

    public function saveComment(int $enrollmentId, string $body): void
    {
        $course = Course::find($this->selectedCourseId);

        if (! $course || Gate::denies('edit-course-grades', $course)) {
            abort(403);
        }

        $result = Result::firstOrCreate(
            ['enrollment_id' => $enrollmentId],
            ['result_type_id' => ResultType::first()?->id ?? 1],
        );

        $existingComment = $result->comments()->first();

        if ($body === '') {
            $existingComment?->delete();
        } elseif ($existingComment) {
            $existingComment->update(['body' => $body]);
        } else {
            $result->comments()->create(['body' => $body]);
        }

        $this->comments[$enrollmentId] = $body;

        Notification::make()
            ->success()
            ->title(__('Comment saved'))
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
