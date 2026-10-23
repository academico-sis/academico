<?php

namespace Tests\Feature;

use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Courses\Pages\ListCourses;
use App\Models\Campus;
use App\Models\Config;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use Database\Seeders\PermissionsSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseFormTest extends TestCase
{
    use RefreshDatabase;

    private Period $period;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 3, 'name' => json_encode(['fr' => 'Cancelled'])],
        ]);

        $this->seed(PermissionsSeeder::class);

        // courses created from the form belong to the default (internal) campus
        Campus::factory()->create(['id' => 1]);

        $this->period = Period::factory()->create(['year_id' => Year::factory()->create()->id]);
        Config::where('name', 'current_period')->update(['value' => $this->period->id]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    private function courseFormData(array $data = []): array
    {
        return [
            'rhythm_id' => Rhythm::factory()->create()->id,
            'name' => 'French A1',
            'price' => 100,
            'spots' => 10,
            'period_id' => $this->period->id,
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-15',
            ...$data,
        ];
    }

    public function test_course_with_a_weekly_schedule_can_be_created(): void
    {
        Livewire::test(CreateCourse::class)
            ->fillForm($this->courseFormData(['courseTimes' => [['day' => 3, 'start' => '09:00', 'end' => '11:00']]]))
            ->call('create')
            ->assertHasNoFormErrors();

        $course = Course::where('name', 'French A1')->sole();
        $this->assertGreaterThan(0, $course->events()->count());
    }

    public function test_course_cannot_end_before_it_starts(): void
    {
        Livewire::test(CreateCourse::class)
            ->fillForm($this->courseFormData(['start_date' => '2026-12-01', 'end_date' => '2026-09-15']))
            ->call('create')
            ->assertHasFormErrors(['end_date' => 'after_or_equal']);

        $this->assertFalse(Course::where('name', 'French A1')->exists());
    }

    public function test_course_time_cannot_end_before_it_starts(): void
    {
        Livewire::test(CreateCourse::class)
            ->fillForm($this->courseFormData(['courseTimes' => [['day' => 3, 'start' => '09:00', 'end' => '08:00']]]))
            ->call('create')
            ->assertHasFormErrors(['courseTimes.0.end' => 'after']);

        $this->assertFalse(Course::where('name', 'French A1')->exists());
    }

    public function test_course_without_enrollments_can_be_deleted(): void
    {
        $course = Course::factory()->create(['period_id' => $this->period->id]);

        Livewire::test(EditCourse::class, ['record' => $course->id])
            ->callAction('delete');

        $this->assertModelMissing($course);
    }

    public function test_course_with_enrollments_cannot_be_deleted(): void
    {
        $course = Course::factory()->create(['period_id' => $this->period->id]);
        Enrollment::create(['student_id' => Student::factory()->create()->id, 'course_id' => $course->id, 'status_id' => 3]);

        Livewire::test(EditCourse::class, ['record' => $course->id])
            ->assertActionDisabled('delete');

        $this->assertModelExists($course);
    }

    public function test_course_whose_submodule_has_enrollments_cannot_be_deleted(): void
    {
        $course = Course::factory()->create(['period_id' => $this->period->id]);
        $submodule = Course::factory()->create(['period_id' => $this->period->id, 'parent_course_id' => $course->id]);
        Enrollment::create(['student_id' => Student::factory()->create()->id, 'course_id' => $submodule->id, 'status_id' => 1]);

        $this->assertTrue($course->hasEnrollments());

        Livewire::test(EditCourse::class, ['record' => $course->id])
            ->assertActionDisabled('delete');
    }

    public function test_bulk_delete_skips_courses_with_enrollments(): void
    {
        $emptyCourse = Course::factory()->create(['period_id' => $this->period->id]);
        $enrolledCourse = Course::factory()->create(['period_id' => $this->period->id]);
        Student::factory()->create()->enroll($enrolledCourse);

        Livewire::test(ListCourses::class)
            ->selectTableRecords([$emptyCourse->id, $enrolledCourse->id])
            ->callAction(TestAction::make('delete')->table()->bulk());

        $this->assertModelMissing($emptyCourse);
        $this->assertModelExists($enrolledCourse);
    }
}
