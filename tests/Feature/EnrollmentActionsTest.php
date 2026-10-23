<?php

namespace Tests\Feature;

use App\Filament\Resources\Courses\Pages\CourseEnrollments;
use App\Filament\Resources\Enrollments\Pages\ChangeEnrollmentCourse;
use App\Filament\Resources\Students\Pages\EnrollStudent;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use App\Services\Settings;
use Database\Seeders\PermissionsSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Every screen that puts a student in a course must enroll in submodules,
 * and refuse duplicates and full courses.
 */
class EnrollmentActionsTest extends TestCase
{
    use RefreshDatabase;

    private Period $period;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
            ['id' => 3, 'name' => json_encode(['fr' => 'Cancelled'])],
        ]);

        $this->seed(PermissionsSeeder::class);

        $this->period = Period::factory()->create(['year_id' => Year::factory()->create()->id]);
        app(Settings::class)->set(['current_period' => $this->period->id]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    private function createCourse(array $attributes = []): Course
    {
        return Course::factory()->create(['period_id' => $this->period->id, 'spots' => 10, ...$attributes]);
    }

    public function test_enroll_student_page_enrolls_in_the_course_and_its_submodules(): void
    {
        $student = Student::factory()->create();
        $course = $this->createCourse();
        $submodule = $this->createCourse(['parent_course_id' => $course->id]);

        Livewire::test(EnrollStudent::class, ['record' => $student->id])
            ->callAction(TestAction::make('enroll')->table($course));

        $enrollment = Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->sole();
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $submodule->id,
            'parent_id' => $enrollment->id,
        ]);
    }

    public function test_enroll_student_page_disables_full_courses_and_existing_enrollments(): void
    {
        $student = Student::factory()->create();
        $openCourse = $this->createCourse();
        $fullCourse = $this->createCourse(['spots' => 1]);
        Student::factory()->create()->enroll($fullCourse);
        $student->enroll($enrolledCourse = $this->createCourse());

        Livewire::test(EnrollStudent::class, ['record' => $student->id])
            ->assertActionEnabled(TestAction::make('enroll')->table($openCourse))
            ->assertActionDisabled(TestAction::make('enroll')->table($fullCourse))
            ->assertActionDisabled(TestAction::make('enroll')->table($enrolledCourse));
    }

    public function test_course_enrollments_page_refuses_a_duplicate_enrollment(): void
    {
        $student = Student::factory()->create();
        $course = $this->createCourse();

        Livewire::test(CourseEnrollments::class, ['record' => $course->id])
            ->callAction('enroll_student', data: ['student_id' => $student->id])
            ->assertHasNoActionErrors()
            ->callAction('enroll_student', data: ['student_id' => $student->id])
            ->assertHasActionErrors(['student_id']);

        $this->assertEquals(1, Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->count());
    }

    public function test_course_enrollments_page_disables_enrolling_in_a_full_course(): void
    {
        $course = $this->createCourse(['spots' => 1]);
        Student::factory()->create()->enroll($course);

        Livewire::test(CourseEnrollments::class, ['record' => $course->id])
            ->assertActionDisabled('enroll_student');
    }

    public function test_change_course_page_refuses_full_courses(): void
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::find($student->enroll($this->createCourse()));
        $openCourse = $this->createCourse();
        $fullCourse = $this->createCourse(['spots' => 1]);
        Student::factory()->create()->enroll($fullCourse);

        Livewire::test(ChangeEnrollmentCourse::class, ['record' => $enrollment->id])
            ->assertActionDisabled(TestAction::make('select')->table($fullCourse))
            ->callAction(TestAction::make('select')->table($openCourse));

        $this->assertEquals($openCourse->id, $enrollment->fresh()->course_id);
    }
}
