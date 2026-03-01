<?php

namespace App\Filament\Pages;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Skills\SkillEvaluation;
use App\Models\Skills\SkillScale;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SkillEvaluationPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.skill-evaluation';

    public ?int $selectedPeriodId = null;

    public ?int $selectedCourseId = null;

    public ?int $selectedEnrollmentId = null;

    /** @var array<int, array<string, mixed>> */
    public array $courses = [];

    /** @var array<int, array<string, mixed>> */
    public array $enrollments = [];

    /** @var array<int, array<string, mixed>> */
    public array $skills = [];

    /** @var array<int, array<string, mixed>> */
    public array $scales = [];

    /** @var array<int, int|null> */
    public array $evaluations = [];

    public function mount(): void
    {
        $period = Period::get_default_period();
        $this->selectedPeriodId = $period?->id;
        $this->scales = SkillScale::orderBy('value')->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'shortname' => $s->shortname,
                'value' => $s->value,
                'color' => $s->color,
            ])
            ->toArray();
        $this->loadCourses();
    }

    public function updatedSelectedPeriodId(): void
    {
        $this->selectedCourseId = null;
        $this->selectedEnrollmentId = null;
        $this->enrollments = [];
        $this->skills = [];
        $this->evaluations = [];
        $this->loadCourses();
    }

    public function updatedSelectedCourseId(): void
    {
        $this->selectedEnrollmentId = null;
        $this->skills = [];
        $this->evaluations = [];
        $this->loadEnrollments();
    }

    public function updatedSelectedEnrollmentId(): void
    {
        $this->loadSkillsAndEvaluations();
    }

    protected function loadCourses(): void
    {
        if (! $this->selectedPeriodId) {
            return;
        }

        $this->courses = Course::where('period_id', $this->selectedPeriodId)
            ->whereHas('enrollments')
            ->whereHas('evaluationType')
            ->orderBy('name')
            ->get()
            ->map(fn ($course) => [
                'id' => $course->id,
                'name' => $course->name,
            ])
            ->toArray();
    }

    protected function loadEnrollments(): void
    {
        if (! $this->selectedCourseId) {
            return;
        }

        $this->enrollments = Enrollment::with('student')
            ->where('course_id', $this->selectedCourseId)
            ->whereDoesntHave('childrenEnrollments')
            ->get()
            ->sortBy(fn ($e) => $e->student?->name)
            ->map(fn ($e) => [
                'id' => $e->id,
                'studentName' => $e->student?->name ?? '',
            ])
            ->values()
            ->toArray();
    }

    protected function loadSkillsAndEvaluations(): void
    {
        if (! $this->selectedEnrollmentId || ! $this->selectedCourseId) {
            return;
        }

        $course = Course::find($this->selectedCourseId);

        $courseSkills = $course?->skills()?->get() ?? collect();

        $this->skills = $courseSkills->map(fn ($skill) => [
            'id' => $skill->id,
            'name' => $skill->name,
            'typeName' => $skill->skillType?->name ?? '',
            'levelName' => $skill->level?->name ?? '',
        ])->toArray();

        $existingEvals = SkillEvaluation::where('enrollment_id', $this->selectedEnrollmentId)
            ->whereIn('skill_id', $courseSkills->pluck('id'))
            ->get()
            ->keyBy('skill_id');

        $evals = [];
        foreach ($courseSkills as $skill) {
            $eval = $existingEvals->get($skill->id);
            $evals[$skill->id] = $eval?->skill_scale_id;
        }

        $this->evaluations = $evals;
    }

    public function setEvaluation(int $skillId, int $scaleId): void
    {
        if (! $this->selectedEnrollmentId) {
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

        Notification::make()
            ->success()
            ->title(__('Skill evaluation saved'))
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

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Skill Evaluation');
    }
}
