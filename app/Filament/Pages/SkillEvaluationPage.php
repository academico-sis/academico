<?php

namespace App\Filament\Pages;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Result;
use App\Models\ResultType;
use App\Models\Skills\SkillEvaluation;
use App\Models\Skills\SkillScale;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Gate;

class SkillEvaluationPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('evaluation.view') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected string $view = 'filament.pages.skill-evaluation';

    public int $courseId;

    public string $courseName = '';

    public ?int $selectedEnrollmentId = null;

    public bool $isReadOnly = false;

    /** @var array<int, array<string, mixed>> */
    public array $enrollments = [];

    /** @var array<int, array<string, mixed>> */
    public array $skills = [];

    /** @var array<int, array<string, mixed>> */
    public array $scales = [];

    /** @var array<int, int|null> */
    public array $evaluations = [];

    /** @var array<string, int|null> Matrix: "enrollmentId-skillId" => scaleId */
    public array $allEvaluations = [];

    /** @var array<int, array<string, mixed>> */
    public array $resultTypes = [];

    /** @var array<int, array<string, mixed>> enrollmentId => {resultTypeId, resultTypeColor} */
    public array $enrollmentResults = [];

    /** @var array<int, string> enrollmentId => comment body */
    public array $comments = [];

    public function mount(): void
    {
        $courseId = request()->integer('courseId') ?: null;
        $course = $courseId ? Course::find($courseId) : null;

        abort_unless($course, 404);
        abort_unless(Gate::allows('view-course-grades', $course), 403);

        $this->courseId = $course->id;
        $this->courseName = $course->name;

        $this->isReadOnly = Gate::denies('edit-course-grades', $course);

        $this->scales = SkillScale::orderBy('value')->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'shortname' => $s->shortname,
                'value' => $s->value,
                'color' => $s->color,
            ])
            ->toArray();

        $this->resultTypes = ResultType::all()
            ->map(fn ($rt) => [
                'id' => $rt->id,
                'name' => $rt->name,
                'color' => $rt->color,
            ])
            ->toArray();

        $this->loadEnrollments();
        $this->loadSkills();
        $this->loadAllEvaluations();
    }

    protected function loadEnrollments(): void
    {
        $this->enrollmentResults = [];
        $this->comments = [];

        $enrollments = Enrollment::with(['student', 'result.result_name', 'result.comments'])
            ->where('course_id', $this->courseId)
            ->whereDoesntHave('childrenEnrollments')
            ->get()
            ->sortBy(fn ($e) => $e->student?->name);

        $this->enrollments = $enrollments->map(function ($e) {
            $resultComment = $e->result?->comments?->first();
            $this->comments[$e->id] = $resultComment?->body ?? '';

            $this->enrollmentResults[$e->id] = [
                'resultTypeId' => $e->result?->result_type_id,
                'resultTypeColor' => $e->result?->result_name?->color,
            ];

            return [
                'id' => $e->id,
                'studentName' => $e->student?->name ?? '',
            ];
        })
            ->values()
            ->toArray();
    }

    protected function loadSkills(): void
    {
        $course = Course::find($this->courseId);
        $courseSkills = $course?->skills()?->get() ?? collect();

        $this->skills = $courseSkills->map(fn ($skill) => [
            'id' => $skill->id,
            'name' => $skill->name,
            'typeName' => $skill->skillType?->name ?? '',
            'levelName' => $skill->level?->name ?? '',
        ])->toArray();
    }

    protected function loadAllEvaluations(): void
    {
        $enrollmentIds = collect($this->enrollments)->pluck('id');
        $skillIds = collect($this->skills)->pluck('id');

        $evals = SkillEvaluation::whereIn('enrollment_id', $enrollmentIds)
            ->whereIn('skill_id', $skillIds)
            ->get();

        $matrix = [];
        foreach ($evals as $eval) {
            $matrix[$eval->enrollment_id.'-'.$eval->skill_id] = $eval->skill_scale_id;
        }

        $this->allEvaluations = $matrix;
    }

    protected function loadSkillsAndEvaluations(): void
    {
        if (! $this->selectedEnrollmentId) {
            return;
        }

        $skillIds = collect($this->skills)->pluck('id');

        $existingEvals = SkillEvaluation::where('enrollment_id', $this->selectedEnrollmentId)
            ->whereIn('skill_id', $skillIds)
            ->get()
            ->keyBy('skill_id');

        $evals = [];
        foreach ($this->skills as $skill) {
            $eval = $existingEvals->get($skill['id']);
            $evals[$skill['id']] = $eval?->skill_scale_id;
        }

        $this->evaluations = $evals;
    }

    public function selectStudent(int $enrollmentId): void
    {
        $this->selectedEnrollmentId = $enrollmentId;
        $this->loadSkillsAndEvaluations();
    }

    public function backToOverview(): void
    {
        $this->selectedEnrollmentId = null;
        $this->evaluations = [];
        $this->loadAllEvaluations();
    }

    public function nextStudent(): void
    {
        $index = $this->getCurrentStudentIndex();
        if ($index !== null && $index < count($this->enrollments) - 1) {
            $this->selectStudent($this->enrollments[$index + 1]['id']);
        }
    }

    public function previousStudent(): void
    {
        $index = $this->getCurrentStudentIndex();
        if ($index !== null && $index > 0) {
            $this->selectStudent($this->enrollments[$index - 1]['id']);
        }
    }

    public function getCurrentStudentIndex(): ?int
    {
        if (! $this->selectedEnrollmentId) {
            return null;
        }

        foreach ($this->enrollments as $i => $enrollment) {
            if ($enrollment['id'] === $this->selectedEnrollmentId) {
                return $i;
            }
        }

        return null;
    }

    public function setEvaluation(int $skillId, int $scaleId): void
    {
        if (! $this->selectedEnrollmentId || $this->isReadOnly) {
            return;
        }

        SkillEvaluation::updateOrCreate(
            [
                'enrollment_id' => $this->selectedEnrollmentId,
                'skill_id' => $skillId,
            ],
            [
                'skill_scale_id' => $scaleId,
            ],
        );

        $this->evaluations[$skillId] = $scaleId;
        $this->allEvaluations[$this->selectedEnrollmentId.'-'.$skillId] = $scaleId;

        Notification::make()
            ->success()
            ->title(__('Skill evaluation saved'))
            ->duration(1500)
            ->send();
    }

    public function setEvaluationFromMatrix(int $enrollmentId, int $skillId, int $scaleId): void
    {
        if ($this->isReadOnly) {
            return;
        }

        SkillEvaluation::updateOrCreate(
            [
                'enrollment_id' => $enrollmentId,
                'skill_id' => $skillId,
            ],
            [
                'skill_scale_id' => $scaleId,
            ],
        );

        $this->allEvaluations[$enrollmentId.'-'.$skillId] = $scaleId;

        Notification::make()
            ->success()
            ->title(__('Skill evaluation saved'))
            ->duration(1500)
            ->send();
    }

    public function saveResult(int $enrollmentId, string $resultTypeId): void
    {
        $course = Course::find($this->courseId);

        if (! $course || Gate::denies('edit-course-grades', $course)) {
            abort(403);
        }

        if ($resultTypeId === '') {
            Result::where('enrollment_id', $enrollmentId)->delete();
            $this->enrollmentResults[$enrollmentId] = [
                'resultTypeId' => null,
                'resultTypeColor' => null,
            ];
        } else {
            Result::updateOrCreate(
                ['enrollment_id' => $enrollmentId],
                ['result_type_id' => (int) $resultTypeId],
            );

            $resultType = ResultType::find((int) $resultTypeId);
            $this->enrollmentResults[$enrollmentId] = [
                'resultTypeId' => $resultType?->id,
                'resultTypeColor' => $resultType?->color,
            ];
        }

        Notification::make()
            ->success()
            ->title(__('Result saved'))
            ->duration(1500)
            ->send();
    }

    public function saveComment(int $enrollmentId, string $body): void
    {
        $course = Course::find($this->courseId);

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
        return __('Skill Evaluation');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Skill Evaluation');
    }
}
