<?php

namespace Tests\Feature;

use App\Filament\Pages\CourseAttendance;
use App\Filament\Pages\EventAttendance;
use App\Filament\Pages\GradeEdit;
use App\Filament\Pages\SkillEvaluationPage;
use App\Filament\Pages\TeacherDashboard;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationType;
use App\Models\Event;
use App\Models\GradeType;
use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AttendancePagesTest extends TestCase
{
    use RefreshDatabase;

    private Teacher $teacher;

    private Course $course;

    private Event $event;

    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
        ]);
        DB::table('attendance_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Present'])],
            ['id' => 4, 'name' => json_encode(['fr' => 'Absent'])],
        ]);

        Permission::findOrCreate('attendance.view', 'web');
        Role::findOrCreate('secretary', 'web')->givePermissionTo('attendance.view');
        Role::findOrCreate('viewer', 'web');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $period = Period::factory()->create();
        app(Settings::class)->set(['current_period' => $period->id]);

        $this->teacher = Teacher::factory()->create();
        $this->course = Course::factory()->create(['period_id' => $period->id, 'teacher_id' => $this->teacher->id]);
        // Enrolling adds attendance to the past events of the course, so students are enrolled first.
        $this->student = $this->enrolledIn($this->course);
        $this->event = $this->eventOf($this->course);
    }

    public function test_teacher_takes_attendance_for_their_own_event(): void
    {
        $this->actingAs($this->teacher->user);

        $this->get(EventAttendance::getUrl(['eventId' => $this->event->id]))->assertOk();

        Livewire::withQueryParams(['eventId' => $this->event->id])
            ->test(EventAttendance::class)
            ->assertSee($this->student->name)
            ->call('toggleAttendance', $this->student->id, 4);

        $this->assertDatabaseHas('attendances', [
            'event_id' => $this->event->id,
            'student_id' => $this->student->id,
            'attendance_type_id' => 4,
        ]);
    }

    public function test_teacher_cannot_open_the_attendance_of_another_teacher_event(): void
    {
        $this->actingAs(Teacher::factory()->create()->user);

        $this->get(EventAttendance::getUrl(['eventId' => $this->event->id]))->assertForbidden();
    }

    public function test_staff_without_permission_cannot_open_the_attendance_pages(): void
    {
        $this->actingAs(User::factory()->create()->assignRole('viewer'));

        $this->get(EventAttendance::getUrl(['eventId' => $this->event->id]))->assertForbidden();
        $this->get(CourseAttendance::getUrl(['courseId' => $this->course->id]))->assertForbidden();
    }

    public function test_staff_with_permission_open_the_attendance_pages(): void
    {
        $this->actingAs(User::factory()->create()->assignRole('secretary'));

        $this->get(EventAttendance::getUrl(['eventId' => $this->event->id]))->assertOk();
        $this->get(CourseAttendance::getUrl(['courseId' => $this->course->id]))->assertOk();
    }

    public function test_event_of_the_attendance_page_cannot_be_changed_by_the_client(): void
    {
        $this->actingAs($this->teacher->user);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::withQueryParams(['eventId' => $this->event->id])
            ->test(EventAttendance::class)
            ->set('eventId', Event::factory()->create()->id);
    }

    public function test_event_attendance_is_refused_for_students_outside_the_course(): void
    {
        $this->actingAs($this->teacher->user);
        $otherStudent = $this->enrolledIn(Course::factory()->create());

        Livewire::withQueryParams(['eventId' => $this->event->id])
            ->test(EventAttendance::class)
            ->call('toggleAttendance', $otherStudent->id, 4)
            ->assertForbidden();

        $this->assertDatabaseMissing('attendances', ['student_id' => $otherStudent->id]);
    }

    public function test_teacher_takes_attendance_for_their_own_course(): void
    {
        $this->actingAs($this->teacher->user);

        $this->get(CourseAttendance::getUrl(['courseId' => $this->course->id]))->assertOk();

        Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(CourseAttendance::class)
            ->assertSee($this->student->name)
            ->call('toggleAttendance', $this->student->id, $this->event->id, 1);

        $this->assertDatabaseHas('attendances', [
            'event_id' => $this->event->id,
            'student_id' => $this->student->id,
            'attendance_type_id' => 1,
        ]);
    }

    public function test_teacher_cannot_open_the_attendance_of_another_teacher_course(): void
    {
        $this->actingAs(Teacher::factory()->create()->user);

        $this->get(CourseAttendance::getUrl(['courseId' => $this->course->id]))->assertForbidden();
    }

    public function test_course_attendance_is_refused_for_events_and_students_of_other_courses(): void
    {
        $this->actingAs($this->teacher->user);
        $otherCourse = Course::factory()->create(['teacher_id' => $this->teacher->id]);
        $otherStudent = $this->enrolledIn($otherCourse);
        $otherEvent = $this->eventOf($otherCourse);

        Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(CourseAttendance::class)
            ->call('toggleAttendance', $this->student->id, $otherEvent->id, 1)
            ->assertForbidden();
        Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(CourseAttendance::class)
            ->call('toggleAttendance', $otherStudent->id, $this->event->id, 1)
            ->assertForbidden();

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_course_of_the_attendance_page_cannot_be_changed_by_the_client(): void
    {
        $this->actingAs($this->teacher->user);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::withQueryParams(['courseId' => $this->course->id])
            ->test(CourseAttendance::class)
            ->set('courseId', Course::factory()->create()->id);
    }

    public function test_teacher_dashboard_links_to_the_attendance_and_evaluation_pages(): void
    {
        $evaluationType = EvaluationType::factory()->create();
        DB::table('evaluation_type_presets')->insert([
            'evaluation_type_id' => $evaluationType->id,
            'presettable_type' => GradeType::class,
            'presettable_id' => GradeType::factory()->create()->id,
        ]);
        $this->course->update(['evaluation_type_id' => $evaluationType->id]);
        $this->actingAs($this->teacher->user);

        Livewire::test(TeacherDashboard::class)
            ->assertSee(EventAttendance::getUrl(['eventId' => $this->event->id]))
            ->assertSee(CourseAttendance::getUrl(['courseId' => $this->course->id]))
            ->assertSee(GradeEdit::getUrl(['courseId' => $this->course->id]))
            ->assertDontSee(SkillEvaluationPage::getUrl(['courseId' => $this->course->id]));
    }

    private function eventOf(Course $course): Event
    {
        return Event::factory()->create([
            'course_id' => $course->id,
            'teacher_id' => $course->teacher_id,
            'start' => now()->subDay()->setTime(10, 0),
            'end' => now()->subDay()->setTime(12, 0),
        ]);
    }

    private function enrolledIn(Course $course): Student
    {
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'course_id' => $course->id, 'status_id' => 1]);

        return $student;
    }
}
