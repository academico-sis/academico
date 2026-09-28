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
use App\Models\Teacher;
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

    public function test_page_loads_with_course_id(): void
    {
        $student = Student::factory()->create();
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class);

        $component->assertSet('courseId', $this->course->id);
        $component->assertSet('courseName', $this->course->name);
        $component->assertSet('selectedEnrollmentId', null);
    }

    public function test_page_aborts_without_course_id(): void
    {
        $this->get(GradeEdit::getUrl())->assertNotFound();
    }

    public function test_course_teacher_can_view_grades(): void
    {
        $teacher = Teacher::factory()->create();
        $this->course->update(['teacher_id' => $teacher->id]);
        $student = Student::factory()->create();
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $this->actingAs($teacher->user);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class);

        $component->assertSet('isReadOnly', false);
        $this->assertNotEmpty($component->get('enrollments'));
    }

    public function test_other_teacher_cannot_view_grades(): void
    {
        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();
        $this->course->update(['teacher_id' => $otherTeacher->id]);
        $student = Student::factory()->create();
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $this->actingAs($teacher->user);

        Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class)
            ->assertForbidden();
    }

    public function test_grade_data_loaded_on_mount(): void
    {
        $student = Student::factory()->create();
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class);

        $gradeTypes = $component->get('gradeTypes');
        $enrollments = $component->get('enrollments');

        $this->assertNotEmpty($gradeTypes);
        $this->assertNotEmpty($enrollments);
    }

    public function test_select_student_sets_enrollment_id(): void
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class)
            ->call('selectStudent', $enrollment->id);

        $component->assertSet('selectedEnrollmentId', $enrollment->id);
    }

    public function test_back_to_overview_clears_selection(): void
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class)
            ->call('selectStudent', $enrollment->id)
            ->call('backToOverview');

        $component->assertSet('selectedEnrollmentId', null);
    }

    public function test_next_and_previous_student_navigation(): void
    {
        $student1 = Student::factory()->create([
            'id' => User::factory()->create(['firstname' => 'Alice', 'lastname' => 'Aardvark']),
        ]);
        $student2 = Student::factory()->create([
            'id' => User::factory()->create(['firstname' => 'Bob', 'lastname' => 'Baker']),
        ]);

        $enrollment1 = Enrollment::create([
            'student_id' => $student1->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);
        $enrollment2 = Enrollment::create([
            'student_id' => $student2->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $component = Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class);

        // Get the first enrollment from the sorted list
        $enrollments = $component->get('enrollments');
        $firstEnrollmentId = $enrollments[0]['enrollmentId'];
        $secondEnrollmentId = $enrollments[1]['enrollmentId'];

        $component->call('selectStudent', $firstEnrollmentId)
            ->call('nextStudent')
            ->assertSet('selectedEnrollmentId', $secondEnrollmentId)
            ->call('previousStudent')
            ->assertSet('selectedEnrollmentId', $firstEnrollmentId);
    }

    public function test_save_grade_persists_to_database(): void
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class)
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

        Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class)
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

        Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(GradeEdit::class)
            ->call('saveGrade', $enrollment->id, $this->gradeType->id, '18');

        $this->assertEquals(1, Grade::where('enrollment_id', $enrollment->id)->where('grade_type_id', $this->gradeType->id)->count());
        $this->assertEquals(18, Grade::where('enrollment_id', $enrollment->id)->where('grade_type_id', $this->gradeType->id)->first()->grade);
    }
}
