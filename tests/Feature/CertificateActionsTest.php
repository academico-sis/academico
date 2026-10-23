<?php

namespace Tests\Feature;

use App\Filament\Pages\GradeEdit;
use App\Filament\Pages\TeacherDashboard;
use App\Filament\Resources\Courses\Pages\CourseEnrollments;
use App\Filament\Resources\Enrollments\Pages\ViewEnrollment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Result;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\Settings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CertificateActionsTest extends TestCase
{
    use RefreshDatabase;

    private Teacher $teacher;

    private Course $course;

    private Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
        ]);

        foreach (['enrollments.view', 'courses.view', 'evaluation.view', 'evaluation.edit'] as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $period = Period::factory()->create();
        app(Settings::class)->set(['current_period' => $period->id]);

        $this->teacher = Teacher::factory()->create();
        $this->course = Course::factory()->create([
            'name' => 'French A1',
            'period_id' => $period->id,
            'teacher_id' => $this->teacher->id,
        ]);

        $student = Student::factory()->create([
            'id' => User::factory()->create(['firstname' => 'Jane', 'lastname' => 'Doe'])->id,
        ]);
        $this->enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 2,
        ]);
    }

    public function test_enrollment_page_offers_the_result_sheet(): void
    {
        $this->actingAsStaff(['enrollments.view', 'evaluation.view']);
        Result::factory()->create(['enrollment_id' => $this->enrollment->id]);

        $this->enrollmentPage()
            ->callAction($this->evaluationAction('downloadResult'))
            ->assertFileDownloaded('result-jane-doe.pdf');
    }

    public function test_enrollment_page_offers_the_certificate_when_enabled(): void
    {
        $this->actingAsStaff(['enrollments.view', 'evaluation.view']);
        Result::factory()->create(['enrollment_id' => $this->enrollment->id]);
        app(Settings::class)->set(['certificates.enabled' => true]);

        $this->enrollmentPage()
            ->callAction($this->evaluationAction('downloadCertificate'))
            ->assertFileDownloaded('certificate-jane-doe.pdf');
    }

    public function test_certificate_is_hidden_when_certificates_are_disabled(): void
    {
        $this->actingAsStaff(['enrollments.view', 'evaluation.view']);
        Result::factory()->create(['enrollment_id' => $this->enrollment->id]);

        $this->enrollmentPage()
            ->assertActionVisible($this->evaluationAction('downloadResult'))
            ->assertActionDoesNotExist($this->evaluationAction('downloadCertificate'));
    }

    public function test_certificate_is_hidden_without_result(): void
    {
        $this->actingAsStaff(['enrollments.view', 'evaluation.view']);
        app(Settings::class)->set(['certificates.enabled' => true]);

        $this->enrollmentPage()
            ->assertActionDoesNotExist($this->evaluationAction('downloadCertificate'));
    }

    public function test_documents_are_hidden_from_staff_who_cannot_see_evaluations(): void
    {
        $this->actingAsStaff(['enrollments.view']);
        Result::factory()->create(['enrollment_id' => $this->enrollment->id]);
        app(Settings::class)->set(['certificates.enabled' => true]);

        $this->enrollmentPage()
            ->assertActionDoesNotExist($this->evaluationAction('downloadResult'))
            ->assertActionDoesNotExist($this->evaluationAction('downloadCertificate'));
    }

    public function test_course_page_offers_the_course_results(): void
    {
        $this->actingAsStaff(['courses.view', 'evaluation.view']);

        Livewire::test(CourseEnrollments::class, ['record' => $this->course->id])
            ->callAction('downloadCourseResults')
            ->assertFileDownloaded('results-french-a1.pdf');
    }

    public function test_course_results_are_hidden_from_staff_who_cannot_see_grades(): void
    {
        $this->actingAsStaff(['courses.view']);

        Livewire::test(CourseEnrollments::class, ['record' => $this->course->id])
            ->assertActionHidden('downloadCourseResults');
    }

    public function test_grades_page_offers_the_course_results(): void
    {
        $this->actingAsStaff(['evaluation.edit']);

        Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class)
            ->callAction('downloadCourseResults')
            ->assertFileDownloaded('results-french-a1.pdf');
    }

    public function test_teacher_dashboard_links_to_the_documents_of_the_teacher_courses(): void
    {
        $this->actingAs($this->teacher->user);
        Result::factory()->create(['enrollment_id' => $this->enrollment->id]);
        app(Settings::class)->set(['certificates.enabled' => true]);

        Livewire::test(TeacherDashboard::class)
            ->assertSee(__('Result sheet'))
            ->assertSee(__('Certificate'))
            ->assertSee(__('Course results'))
            ->callAction(TestAction::make('downloadCertificate')->arguments(['enrollment' => $this->enrollment->id]))
            ->assertFileDownloaded('certificate-jane-doe.pdf')
            ->callAction(TestAction::make('downloadResult')->arguments(['enrollment' => $this->enrollment->id]))
            ->assertFileDownloaded('result-jane-doe.pdf')
            ->callAction(TestAction::make('downloadCourseResults')->arguments(['course' => $this->course->id]))
            ->assertFileDownloaded('results-french-a1.pdf');
    }

    public function test_teacher_dashboard_hides_the_certificate_when_disabled(): void
    {
        $this->actingAs($this->teacher->user);
        Result::factory()->create(['enrollment_id' => $this->enrollment->id]);

        Livewire::test(TeacherDashboard::class)
            ->assertSee(__('Result sheet'))
            ->assertDontSee(__('Certificate'))
            ->assertActionHidden(TestAction::make('downloadCertificate')->arguments(['enrollment' => $this->enrollment->id]));
    }

    public function test_teachers_cannot_download_documents_of_other_courses(): void
    {
        $this->actingAs(Teacher::factory()->create()->user);
        Result::factory()->create(['enrollment_id' => $this->enrollment->id]);
        app(Settings::class)->set(['certificates.enabled' => true]);

        Livewire::test(TeacherDashboard::class)
            ->assertActionHidden(TestAction::make('downloadCertificate')->arguments(['enrollment' => $this->enrollment->id]))
            ->assertActionHidden(TestAction::make('downloadResult')->arguments(['enrollment' => $this->enrollment->id]))
            ->assertActionHidden(TestAction::make('downloadCourseResults')->arguments(['course' => $this->course->id]))
            ->assertActionHidden(TestAction::make('downloadResult')->arguments(['enrollment' => 0]));
    }

    /**
     * @param  list<string>  $permissions
     */
    private function actingAsStaff(array $permissions): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        $this->actingAs($user);
    }

    private function enrollmentPage(): Testable
    {
        return Livewire::test(ViewEnrollment::class, ['record' => $this->enrollment->id]);
    }

    /**
     * An action of the "Result & Evaluation" tab of the enrollment page. Hidden ones cannot be resolved.
     */
    private function evaluationAction(string $name): TestAction
    {
        return TestAction::make($name)->schemaComponent('evaluationActions', schema: 'content');
    }
}
