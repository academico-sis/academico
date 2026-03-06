<?php

namespace Tests\Feature;

use App\Filament\Pages\GradeEdit;
use App\Models\Config;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationType;
use App\Models\Grade;
use App\Models\GradeType;
use App\Models\Period;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GradeEditPageTest extends TestCase
{
    use RefreshDatabase;

    private Period $period;

    private Course $course;

    private EvaluationType $evaluationType;

    private GradeType $gradeType;

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

        $this->evaluationType = EvaluationType::factory()->create();
        $this->gradeType = GradeType::factory()->create();

        // Attach grade type to evaluation type via pivot
        \DB::table('evaluation_type_presets')->insert([
            'evaluation_type_id' => $this->evaluationType->id,
            'presettable_type' => GradeType::class,
            'presettable_id' => $this->gradeType->id,
        ]);

        $this->course = Course::factory()->create([
            'period_id' => $this->period->id,
            'evaluation_type_id' => $this->evaluationType->id,
        ]);

        Permission::findOrCreate('evaluation.edit', 'web');
        $role = Role::findOrCreate('admin', 'web');
        $role->givePermissionTo('evaluation.edit');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    public function test_page_loads_with_default_period(): void
    {
        $component = Livewire::test(GradeEdit::class);

        $component->assertSet('selectedPeriodId', $this->period->id);
    }

    public function test_courses_loaded_for_period(): void
    {
        // Create an enrollment so the course shows up (whereHas enrollments)
        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $component = Livewire::test(GradeEdit::class);

        $courses = $component->get('courses');
        $courseIds = collect($courses)->pluck('id')->toArray();
        $this->assertContains($this->course->id, $courseIds);
    }

    public function test_selecting_course_loads_grade_data(): void
    {
        $student = Student::factory()->create();
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $component = Livewire::test(GradeEdit::class)
            ->set('selectedCourseId', $this->course->id);

        $gradeTypes = $component->get('gradeTypes');
        $enrollments = $component->get('enrollments');

        $this->assertNotEmpty($gradeTypes);
        $this->assertNotEmpty($enrollments);
    }

    public function test_save_grade_persists_to_database(): void
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $component = Livewire::test(GradeEdit::class)
            ->set('selectedCourseId', $this->course->id)
            ->call('saveGrade', $enrollment->id, $this->gradeType->id, '15.5');

        $this->assertDatabaseHas('grades', [
            'enrollment_id' => $enrollment->id,
            'grade_type_id' => $this->gradeType->id,
            'grade' => 15.5,
        ]);
    }

    public function test_save_empty_grade_deletes_record(): void
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        Grade::create([
            'enrollment_id' => $enrollment->id,
            'grade_type_id' => $this->gradeType->id,
            'grade' => 10,
        ]);

        Livewire::test(GradeEdit::class)
            ->set('selectedCourseId', $this->course->id)
            ->call('saveGrade', $enrollment->id, $this->gradeType->id, '');

        $this->assertDatabaseMissing('grades', [
            'enrollment_id' => $enrollment->id,
            'grade_type_id' => $this->gradeType->id,
        ]);
    }

    public function test_save_grade_updates_existing(): void
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        Grade::create([
            'enrollment_id' => $enrollment->id,
            'grade_type_id' => $this->gradeType->id,
            'grade' => 10,
        ]);

        Livewire::test(GradeEdit::class)
            ->set('selectedCourseId', $this->course->id)
            ->call('saveGrade', $enrollment->id, $this->gradeType->id, '18');

        $this->assertEquals(1, Grade::where('enrollment_id', $enrollment->id)->where('grade_type_id', $this->gradeType->id)->count());
        $this->assertEquals(18, Grade::where('enrollment_id', $enrollment->id)->where('grade_type_id', $this->gradeType->id)->first()->grade);
    }
}
