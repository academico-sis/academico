<?php

namespace Tests\Feature;

use App\Filament\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Filament\Resources\Enrollments\Pages\ViewEnrollment;
use App\Filament\Resources\Enrollments\RelationManagers\ScholarshipsRelationManager;
use App\Filament\Resources\Periods\Pages\ListPeriods;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationType;
use App\Models\GradeType;
use App\Models\Period;
use App\Models\Scholarship;
use App\Models\Student;
use App\Models\User;
use App\Services\Settings;
use Database\Seeders\PermissionsSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * The enrollment page, its relation managers and the comments are open to anyone who can view enrollments.
 * Changing them requires `enrollments.edit`; changing the comments of other users requires `comments.edit`.
 * Filament refuses to run hidden actions, so checking that an action is hidden is enough.
 */
class EnrollmentWritePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
        ]);
        $this->seed(PermissionsSeeder::class);

        $period = Period::factory()->create();
        app(Settings::class)->set(['current_period' => $period->id]);

        $this->enrollment = Enrollment::factory()->create([
            'course_id' => Course::factory()->create(['period_id' => $period->id])->id,
            'status_id' => 1,
            'total_price' => 100,
        ]);
    }

    /**
     * @param  list<string>  $permissions
     */
    private function actingAsStaff(string $role, array $permissions = []): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        $user->givePermissionTo($permissions);
        $this->actingAs($user);

        return $user;
    }

    public function test_users_who_can_only_view_enrollments_cannot_change_them(): void
    {
        $this->actingAsStaff('viewer', ['enrollments.view']);

        Livewire::test(ViewEnrollment::class, ['record' => $this->enrollment->id])
            ->assertActionHidden('edit_price')
            ->assertActionHidden('edit_status')
            ->assertActionHidden('change_course')
            ->assertActionHidden('cancel');

        $this->get(EnrollmentResource::getUrl('change-course', ['record' => $this->enrollment]))->assertForbidden();
    }

    public function test_secretaries_can_change_enrollments(): void
    {
        $this->actingAsStaff('secretary');

        Livewire::test(ViewEnrollment::class, ['record' => $this->enrollment->id])
            ->assertActionVisible('edit_price')
            ->assertActionVisible('cancel')
            ->callAction('edit_price', data: ['total_price' => 80]);

        $this->assertEquals(80, $this->enrollment->fresh()->total_price);
        $this->get(EnrollmentResource::getUrl('change-course', ['record' => $this->enrollment]))->assertSuccessful();
    }

    public function test_scholarships_can_only_be_attached_with_the_edit_permission(): void
    {
        $scholarship = Scholarship::factory()->create();
        $relationManager = ['ownerRecord' => $this->enrollment, 'pageClass' => ViewEnrollment::class];

        $this->actingAsStaff('viewer', ['enrollments.view']);
        Livewire::test(ScholarshipsRelationManager::class, $relationManager)
            ->assertActionHidden(TestAction::make('attach')->table());

        $this->actingAsStaff('secretary');
        Livewire::test(ScholarshipsRelationManager::class, $relationManager)
            ->callAction(TestAction::make('attach')->table(), data: ['recordId' => $scholarship->id]);
        $this->assertTrue($this->enrollment->scholarships()->exists());
    }

    public function test_comments_are_added_by_editors_and_changed_by_their_author(): void
    {
        $relationManager = ['ownerRecord' => $this->enrollment, 'pageClass' => ViewEnrollment::class];
        $otherComment = Comment::factory()->create([
            'commentable_type' => $this->enrollment->getMorphClass(),
            'commentable_id' => $this->enrollment->id,
            'author_id' => User::factory()->create()->id,
            'body' => 'Written by someone else',
        ]);

        $this->actingAsStaff('viewer', ['enrollments.view']);
        Livewire::test(CommentsRelationManager::class, $relationManager)
            ->assertActionHidden(TestAction::make('create')->table())
            ->assertActionHidden(TestAction::make('edit')->table($otherComment));

        $secretary = $this->actingAsStaff('secretary');
        Livewire::test(CommentsRelationManager::class, $relationManager)
            ->callAction(TestAction::make('create')->table(), data: ['body' => 'My comment'])
            ->assertActionHidden(TestAction::make('edit')->table($otherComment))
            ->assertActionHidden(TestAction::make('delete')->table($otherComment));

        $ownComment = Comment::where('body', 'My comment')->firstOrFail();
        $this->assertTrue($ownComment->author()->is($secretary));

        Livewire::test(CommentsRelationManager::class, $relationManager)
            ->callAction(TestAction::make('edit')->table($ownComment), data: ['body' => 'Corrected'])
            ->assertHasNoFormErrors();
        $this->assertSame('Corrected', $ownComment->fresh()->body);

        $this->actingAsStaff('admin');
        Livewire::test(CommentsRelationManager::class, $relationManager)
            ->callAction(TestAction::make('delete')->table($otherComment));
        $this->assertModelMissing($otherComment);
    }

    public function test_student_comments_follow_the_same_rules(): void
    {
        $student = Student::factory()->create();

        $this->actingAsStaff('viewer', ['enrollments.view']);
        Livewire::test(CommentsRelationManager::class, ['ownerRecord' => $student, 'pageClass' => ViewStudent::class])
            ->assertActionHidden(TestAction::make('create')->table());

        $this->actingAsStaff('secretary');
        Livewire::test(CommentsRelationManager::class, ['ownerRecord' => $student, 'pageClass' => ViewStudent::class])
            ->assertActionVisible(TestAction::make('create')->table());
    }

    public function test_grade_button_follows_the_grade_permission_instead_of_the_admin_role(): void
    {
        $evaluationType = EvaluationType::factory()->create();
        $evaluationType->gradeTypes()->attach(GradeType::factory()->create());
        $this->enrollment->course->update(['evaluation_type_id' => $evaluationType->id]);
        $gradeButton = TestAction::make('edit_grades')->schemaComponent('evaluationActions', schema: 'content');

        $this->actingAsStaff('secretary');
        Livewire::test(ViewEnrollment::class, ['record' => $this->enrollment->id])
            ->assertActionDoesNotExist($gradeButton);

        $this->actingAsStaff('viewer', ['enrollments.view', 'evaluation.edit']);
        Livewire::test(ViewEnrollment::class, ['record' => $this->enrollment->id])
            ->assertActionVisible($gradeButton);
    }

    public function test_only_users_who_can_edit_periods_can_change_the_current_period(): void
    {
        $other = Period::factory()->create();

        $this->actingAsStaff('secretary');
        Livewire::test(ListPeriods::class)
            ->assertActionHidden(TestAction::make('setAsCurrentPeriod')->table($other))
            ->assertActionHidden(TestAction::make('setAsEnrollmentPeriod')->table($other));

        $this->actingAsStaff('admin');
        Livewire::test(ListPeriods::class)
            ->callAction(TestAction::make('setAsCurrentPeriod')->table($other));
        $this->assertSame($other->id, (int) app(Settings::class)->get('current_period'));
    }

    public function test_unused_permissions_are_removed(): void
    {
        $this->assertFalse(Permission::whereIn('name', ['leads.manage', 'student.edit'])->exists());

        foreach (['leads.manage', 'student.edit'] as $permission) {
            Permission::create(['name' => $permission]);
        }
        $user = $this->actingAsStaff('secretary', ['student.edit']);

        (require database_path('migrations/2026_10_23_191000_remove_unused_permissions.php'))->up();

        $this->assertFalse(Permission::whereIn('name', ['leads.manage', 'student.edit'])->exists());
        $this->assertTrue($user->fresh()->can('enrollments.edit'));
    }
}
