<?php

namespace Tests\Feature;

use App\Filament\Pages\SkillEvaluationPage;
use App\Models\Config;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationType;
use App\Models\Period;
use App\Models\Result;
use App\Models\ResultType;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillEvaluation;
use App\Models\Skills\SkillScale;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SkillEvaluationPageTest extends TestCase
{
    use RefreshDatabase;

    private Period $period;

    private Course $course;

    private Skill $skill;

    private SkillScale $scale;

    private Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
            ['id' => 3, 'name' => json_encode(['fr' => 'Cancelled'])],
        ]);

        $year = Year::factory()->create();
        $this->period = Period::factory()->create(['year_id' => $year->id]);
        Config::where('name', 'current_period')->update(['value' => $this->period->id]);

        $evaluationType = EvaluationType::factory()->create();
        $this->skill = Skill::factory()->create();
        $this->scale = SkillScale::factory()->create();

        // Attach skill to evaluation type
        \DB::table('evaluation_type_presets')->insert([
            'evaluation_type_id' => $evaluationType->id,
            'presettable_type' => Skill::class,
            'presettable_id' => $this->skill->id,
        ]);

        $this->course = Course::factory()->create([
            'period_id' => $this->period->id,
            'evaluation_type_id' => $evaluationType->id,
        ]);

        $student = Student::factory()->create();
        $this->enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        Permission::findOrCreate('evaluation.view', 'web');
        Permission::findOrCreate('evaluation.edit', 'web');
        $role = Role::findOrCreate('admin', 'web');
        $role->givePermissionTo(['evaluation.view', 'evaluation.edit']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    public function test_page_loads_with_course_data(): void
    {
        $component = Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class);

        $component->assertSet('courseId', $this->course->id);
        $component->assertSet('courseName', $this->course->name);

        $scales = $component->get('scales');
        $this->assertNotEmpty($scales);

        $enrollments = $component->get('enrollments');
        $enrollmentIds = collect($enrollments)->pluck('id')->toArray();
        $this->assertContains($this->enrollment->id, $enrollmentIds);

        $skills = $component->get('skills');
        $skillIds = collect($skills)->pluck('id')->toArray();
        $this->assertContains($this->skill->id, $skillIds);
    }

    public function test_page_loads_result_types(): void
    {
        $resultType = ResultType::factory()->create();

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class);

        $resultTypes = $component->get('resultTypes');
        $resultTypeIds = collect($resultTypes)->pluck('id')->toArray();
        $this->assertContains($resultType->id, $resultTypeIds);
    }

    public function test_overview_matrix_loads_evaluations(): void
    {
        SkillEvaluation::create([
            'enrollment_id' => $this->enrollment->id,
            'skill_id' => $this->skill->id,
            'skill_scale_id' => $this->scale->id,
        ]);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class);

        $allEvaluations = $component->get('allEvaluations');
        $key = $this->enrollment->id.'-'.$this->skill->id;
        $this->assertEquals($this->scale->id, $allEvaluations[$key] ?? null);
    }

    public function test_select_student_loads_detail_view(): void
    {
        $component = Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class)
            ->call('selectStudent', $this->enrollment->id);

        $component->assertSet('selectedEnrollmentId', $this->enrollment->id);
        $evaluations = $component->get('evaluations');
        $this->assertArrayHasKey($this->skill->id, $evaluations);
    }

    public function test_back_to_overview_clears_selection(): void
    {
        $component = Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class)
            ->call('selectStudent', $this->enrollment->id)
            ->call('backToOverview');

        $component->assertSet('selectedEnrollmentId', null);
    }

    public function test_set_evaluation_creates_record(): void
    {
        Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class)
            ->call('selectStudent', $this->enrollment->id)
            ->call('setEvaluation', $this->skill->id, $this->scale->id);

        $this->assertDatabaseHas('skill_evaluations', [
            'enrollment_id' => $this->enrollment->id,
            'skill_id' => $this->skill->id,
            'skill_scale_id' => $this->scale->id,
        ]);
    }

    public function test_set_evaluation_from_matrix_creates_record(): void
    {
        Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class)
            ->call('setEvaluationFromMatrix', $this->enrollment->id, $this->skill->id, $this->scale->id);

        $this->assertDatabaseHas('skill_evaluations', [
            'enrollment_id' => $this->enrollment->id,
            'skill_id' => $this->skill->id,
            'skill_scale_id' => $this->scale->id,
        ]);
    }

    public function test_set_evaluation_updates_existing(): void
    {
        // skill_evaluations table has no id column on SQLite (added only for MySQL in migration).
        // Eloquent updateOrCreate needs a primary key to UPDATE; skip on SQLite.
        if (\DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('skill_evaluations has no id column on SQLite; updateOrCreate cannot update.');
        }

        $newScale = SkillScale::factory()->create();

        SkillEvaluation::create([
            'enrollment_id' => $this->enrollment->id,
            'skill_id' => $this->skill->id,
            'skill_scale_id' => $this->scale->id,
        ]);

        Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class)
            ->call('selectStudent', $this->enrollment->id)
            ->call('setEvaluation', $this->skill->id, $newScale->id);

        $this->assertEquals(1, SkillEvaluation::where('enrollment_id', $this->enrollment->id)->where('skill_id', $this->skill->id)->count());
        $this->assertDatabaseHas('skill_evaluations', [
            'enrollment_id' => $this->enrollment->id,
            'skill_id' => $this->skill->id,
            'skill_scale_id' => $newScale->id,
        ]);
    }

    public function test_save_result_creates_record(): void
    {
        $resultType = ResultType::factory()->create();

        Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class)
            ->call('saveResult', $this->enrollment->id, (string) $resultType->id);

        $this->assertDatabaseHas('results', [
            'enrollment_id' => $this->enrollment->id,
            'result_type_id' => $resultType->id,
        ]);
    }

    public function test_save_result_with_empty_string_deletes_result(): void
    {
        $resultType = ResultType::factory()->create();
        Result::create([
            'enrollment_id' => $this->enrollment->id,
            'result_type_id' => $resultType->id,
        ]);

        Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class)
            ->call('saveResult', $this->enrollment->id, '');

        $this->assertDatabaseMissing('results', [
            'enrollment_id' => $this->enrollment->id,
        ]);
    }

    public function test_save_comment_creates_comment(): void
    {
        $resultType = ResultType::factory()->create();

        Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class)
            ->call('saveComment', $this->enrollment->id, 'Great progress');

        $result = Result::where('enrollment_id', $this->enrollment->id)->first();
        $this->assertNotNull($result);
        $this->assertEquals('Great progress', $result->comments()->first()->body);
    }

    public function test_save_comment_with_empty_string_deletes_comment(): void
    {
        $resultType = ResultType::factory()->create();
        $result = Result::create([
            'enrollment_id' => $this->enrollment->id,
            'result_type_id' => $resultType->id,
        ]);
        $result->comments()->create(['body' => 'Old comment']);

        Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class)
            ->call('saveComment', $this->enrollment->id, '');

        $this->assertCount(0, $result->fresh()->comments);
    }

    public function test_read_only_mode_prevents_evaluation(): void
    {
        // Create a user without evaluation.edit permission and not the course teacher
        Permission::findOrCreate('evaluation.view', 'web');
        $viewRole = Role::findOrCreate('viewer', 'web');
        $viewRole->givePermissionTo('evaluation.view');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');
        $this->actingAs($viewer);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class);

        $component->assertSet('isReadOnly', true);

        // Evaluation should not be saved when read-only
        $component->call('selectStudent', $this->enrollment->id)
            ->call('setEvaluation', $this->skill->id, $this->scale->id);

        $this->assertDatabaseMissing('skill_evaluations', [
            'enrollment_id' => $this->enrollment->id,
            'skill_id' => $this->skill->id,
        ]);
    }

    public function test_read_only_mode_prevents_matrix_evaluation(): void
    {
        Permission::findOrCreate('evaluation.view', 'web');
        $viewRole = Role::findOrCreate('viewer', 'web');
        $viewRole->givePermissionTo('evaluation.view');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');
        $this->actingAs($viewer);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class);

        $component->call('setEvaluationFromMatrix', $this->enrollment->id, $this->skill->id, $this->scale->id);

        $this->assertDatabaseMissing('skill_evaluations', [
            'enrollment_id' => $this->enrollment->id,
            'skill_id' => $this->skill->id,
        ]);
    }

    public function test_enrollments_load_existing_results(): void
    {
        $resultType = ResultType::factory()->create();
        Result::create([
            'enrollment_id' => $this->enrollment->id,
            'result_type_id' => $resultType->id,
        ]);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class);

        $enrollmentResults = $component->get('enrollmentResults');
        $this->assertEquals($resultType->id, $enrollmentResults[$this->enrollment->id]['resultTypeId']);
    }

    public function test_enrollments_load_existing_comments(): void
    {
        $resultType = ResultType::factory()->create();
        $result = Result::create([
            'enrollment_id' => $this->enrollment->id,
            'result_type_id' => $resultType->id,
        ]);
        $result->comments()->create(['body' => 'Test comment']);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])->test(SkillEvaluationPage::class);

        $comments = $component->get('comments');
        $this->assertEquals('Test comment', $comments[$this->enrollment->id]);
    }
}
