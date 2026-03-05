<?php

namespace Tests\Feature;

use App\Filament\Pages\SkillEvaluationPage;
use App\Models\Config;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationType;
use App\Models\Period;
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
        $role = Role::findOrCreate('admin', 'web');
        $role->givePermissionTo('evaluation.view');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    public function test_page_loads_skill_scales(): void
    {
        $component = Livewire::test(SkillEvaluationPage::class);

        $scales = $component->get('scales');
        $this->assertNotEmpty($scales);
    }

    public function test_selecting_course_loads_enrollments(): void
    {
        $component = Livewire::test(SkillEvaluationPage::class)
            ->set('selectedCourseId', $this->course->id);

        $enrollments = $component->get('enrollments');
        $enrollmentIds = collect($enrollments)->pluck('id')->toArray();
        $this->assertContains($this->enrollment->id, $enrollmentIds);
    }

    public function test_selecting_enrollment_loads_skills(): void
    {
        $component = Livewire::test(SkillEvaluationPage::class)
            ->set('selectedCourseId', $this->course->id)
            ->set('selectedEnrollmentId', $this->enrollment->id);

        $skills = $component->get('skills');
        $skillIds = collect($skills)->pluck('id')->toArray();
        $this->assertContains($this->skill->id, $skillIds);
    }

    public function test_set_evaluation_creates_record(): void
    {
        Livewire::test(SkillEvaluationPage::class)
            ->set('selectedCourseId', $this->course->id)
            ->set('selectedEnrollmentId', $this->enrollment->id)
            ->call('setEvaluation', $this->skill->id, $this->scale->id);

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

        Livewire::test(SkillEvaluationPage::class)
            ->set('selectedCourseId', $this->course->id)
            ->set('selectedEnrollmentId', $this->enrollment->id)
            ->call('setEvaluation', $this->skill->id, $newScale->id);

        $this->assertEquals(1, SkillEvaluation::where('enrollment_id', $this->enrollment->id)->where('skill_id', $this->skill->id)->count());
        $this->assertDatabaseHas('skill_evaluations', [
            'enrollment_id' => $this->enrollment->id,
            'skill_id' => $this->skill->id,
            'skill_scale_id' => $newScale->id,
        ]);
    }
}
