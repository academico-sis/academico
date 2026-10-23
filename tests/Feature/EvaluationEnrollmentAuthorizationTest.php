<?php

namespace Tests\Feature;

use App\Filament\Pages\GradeEdit;
use App\Filament\Pages\SkillEvaluationPage;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationType;
use App\Models\Grade;
use App\Models\GradeType;
use App\Models\Period;
use App\Models\Result;
use App\Models\ResultType;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillEvaluation;
use App\Models\Skills\SkillScale;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Year;
use App\Services\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * The grade and skill evaluation pages receive enrollment ids from the client:
 * a teacher must not be able to write to the enrollments of another course.
 */
class EvaluationEnrollmentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private Teacher $teacher;

    private Course $ownCourse;

    private Enrollment $ownEnrollment;

    private Enrollment $foreignEnrollment;

    private GradeType $gradeType;

    private Skill $skill;

    private SkillScale $scale;

    private ResultType $resultType;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
        ]);

        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);
        app(Settings::class)->set(['current_period' => $period->id]);

        $evaluationType = EvaluationType::factory()->create();
        $this->gradeType = GradeType::factory()->create();
        $this->skill = Skill::factory()->create();
        $this->scale = SkillScale::factory()->create();
        $this->resultType = ResultType::factory()->create();

        \DB::table('evaluation_type_presets')->insert([
            ['evaluation_type_id' => $evaluationType->id, 'presettable_type' => GradeType::class, 'presettable_id' => $this->gradeType->id],
            ['evaluation_type_id' => $evaluationType->id, 'presettable_type' => Skill::class, 'presettable_id' => $this->skill->id],
        ]);

        $this->teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();

        $this->ownCourse = Course::factory()->create([
            'period_id' => $period->id,
            'evaluation_type_id' => $evaluationType->id,
            'teacher_id' => $this->teacher->id,
        ]);
        $foreignCourse = Course::factory()->create([
            'period_id' => $period->id,
            'evaluation_type_id' => $evaluationType->id,
            'teacher_id' => $otherTeacher->id,
        ]);

        $this->ownEnrollment = Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $this->ownCourse->id,
            'status_id' => 1,
        ]);
        $this->foreignEnrollment = Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $foreignCourse->id,
            'status_id' => 1,
        ]);

        // The skill evaluation page requires evaluation.view to be opened at all
        Permission::findOrCreate('evaluation.view', 'web');
        $this->teacher->user->givePermissionTo('evaluation.view');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($this->teacher->user);
    }

    private function gradeEdit()
    {
        return Livewire::withQueryParams(['courseId' => $this->ownCourse->id])->test(GradeEdit::class);
    }

    private function skillEvaluation()
    {
        return Livewire::withQueryParams(['courseId' => $this->ownCourse->id])->test(SkillEvaluationPage::class);
    }

    public function test_teacher_can_still_grade_their_own_course(): void
    {
        $this->gradeEdit()
            ->call('saveGrade', $this->ownEnrollment->id, $this->gradeType->id, '15')
            ->assertSuccessful();
        $this->skillEvaluation()
            ->call('setEvaluationFromMatrix', $this->ownEnrollment->id, $this->skill->id, $this->scale->id)
            ->assertSuccessful();

        $this->assertDatabaseHas('grades', ['enrollment_id' => $this->ownEnrollment->id, 'grade' => 15]);
        $this->assertDatabaseHas('skill_evaluations', ['enrollment_id' => $this->ownEnrollment->id, 'skill_id' => $this->skill->id]);
    }

    public function test_grade_page_rejects_enrollments_of_another_course(): void
    {
        $this->gradeEdit()->call('saveGrade', $this->foreignEnrollment->id, $this->gradeType->id, '42')->assertForbidden();
        $this->gradeEdit()->call('saveResult', $this->foreignEnrollment->id, (string) $this->resultType->id)->assertForbidden();
        $this->gradeEdit()->call('saveComment', $this->foreignEnrollment->id, 'tampered')->assertForbidden();

        $this->assertSame(0, Grade::where('enrollment_id', $this->foreignEnrollment->id)->count());
        $this->assertSame(0, Result::where('enrollment_id', $this->foreignEnrollment->id)->count());
        $this->assertSame(0, Comment::where('body', 'tampered')->count());
    }

    public function test_skill_evaluation_page_rejects_enrollments_of_another_course(): void
    {
        $this->skillEvaluation()->call('setEvaluationFromMatrix', $this->foreignEnrollment->id, $this->skill->id, $this->scale->id)->assertForbidden();
        $this->skillEvaluation()->call('selectStudent', $this->foreignEnrollment->id)->assertForbidden();
        $this->skillEvaluation()->call('saveResult', $this->foreignEnrollment->id, (string) $this->resultType->id)->assertForbidden();
        $this->skillEvaluation()->call('saveComment', $this->foreignEnrollment->id, 'tampered')->assertForbidden();

        $this->assertSame(0, SkillEvaluation::where('enrollment_id', $this->foreignEnrollment->id)->count());
        $this->assertSame(0, Result::where('enrollment_id', $this->foreignEnrollment->id)->count());
        $this->assertSame(0, Comment::where('body', 'tampered')->count());
    }

    public function test_client_cannot_change_the_course_or_the_read_only_flag(): void
    {
        foreach ([GradeEdit::class, SkillEvaluationPage::class] as $page) {
            foreach (['courseId' => 999, 'isReadOnly' => false, 'selectedEnrollmentId' => $this->foreignEnrollment->id] as $property => $value) {
                try {
                    Livewire::withQueryParams(['courseId' => $this->ownCourse->id])->test($page)->set($property, $value);
                    $this->fail("{$page}::\${$property} can be updated by the client");
                } catch (CannotUpdateLockedPropertyException) {
                    $this->addToAssertionCount(1);
                }
            }
        }
    }

    public function test_read_only_user_cannot_write_skill_evaluations(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole(Role::findOrCreate('evaluation-viewer', 'web')->givePermissionTo('evaluation.view'));
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->actingAs($viewer);

        $this->skillEvaluation()
            ->assertSet('isReadOnly', true)
            ->call('setEvaluationFromMatrix', $this->ownEnrollment->id, $this->skill->id, $this->scale->id)
            ->call('saveResult', $this->ownEnrollment->id, (string) $this->resultType->id)
            ->assertForbidden();

        $this->assertSame(0, SkillEvaluation::count());
        $this->assertSame(0, Result::count());
    }
}
