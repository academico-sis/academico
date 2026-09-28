<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AuthorizationGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
            ['id' => 3, 'name' => json_encode(['fr' => 'Cancelled'])],
        ]);

        foreach ([
            'evaluation.edit', 'attendance.view', 'attendance.edit',
            'calendars.view', 'courses.view', 'evaluation.view',
            'enrollments.edit', 'hr.view',
        ] as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    // ── edit-course-grades ──

    public function test_edit_course_grades_allowed_for_course_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('edit-course-grades', $course));
    }

    public function test_edit_course_grades_denied_for_other_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('edit-course-grades', $course));
    }

    public function test_edit_course_grades_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('evaluation.edit');
        $course = Course::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('edit-course-grades', $course));
    }

    // ── view-course-grades ──

    public function test_view_course_grades_allowed_for_course_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('view-course-grades', $course));
    }

    public function test_view_course_grades_denied_for_other_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('view-course-grades', $course));
    }

    public function test_view_course_grades_allowed_with_view_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('evaluation.view');
        $course = Course::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-course-grades', $course));
    }

    public function test_view_course_grades_allowed_with_edit_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('evaluation.edit');
        $course = Course::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-course-grades', $course));
    }

    public function test_view_course_grades_denied_without_permission(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $this->actingAs($user);
        $this->assertFalse(Gate::allows('view-course-grades', $course));
    }

    // ── view-course-attendance ──

    public function test_view_course_attendance_allowed_for_course_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('view-course-attendance', $course));
    }

    public function test_view_course_attendance_denied_for_other_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('view-course-attendance', $course));
    }

    public function test_view_course_attendance_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('attendance.view');
        $course = Course::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-course-attendance', $course));
    }

    // ── view-event-attendance ──

    public function test_view_event_attendance_allowed_for_event_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $event = Event::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('view-event-attendance', $event));
    }

    public function test_view_event_attendance_allowed_for_course_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $otherTeacher = Teacher::factory()->create();
        $event = Event::factory()->create([
            'course_id' => $course->id,
            'teacher_id' => $otherTeacher->id,
        ]);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('view-event-attendance', $event));
    }

    public function test_view_event_attendance_denied_for_unrelated_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $event = Event::factory()->create();

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('view-event-attendance', $event));
    }

    public function test_view_event_attendance_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('attendance.view');
        $event = Event::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-event-attendance', $event));
    }

    // ── edit-attendance ──

    public function test_edit_attendance_allowed_for_event_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $event = Event::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('edit-attendance', $event));
    }

    public function test_edit_attendance_allowed_for_course_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $otherTeacher = Teacher::factory()->create();
        $event = Event::factory()->create([
            'course_id' => $course->id,
            'teacher_id' => $otherTeacher->id,
        ]);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('edit-attendance', $event));
    }

    public function test_edit_attendance_denied_for_unrelated_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $event = Event::factory()->create();

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('edit-attendance', $event));
    }

    public function test_edit_attendance_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('attendance.edit');
        $event = Event::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('edit-attendance', $event));
    }

    // ── view-teacher-calendar ──

    public function test_view_teacher_calendar_allowed_for_own_calendar(): void
    {
        $teacher = Teacher::factory()->create();

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('view-teacher-calendar', $teacher));
    }

    public function test_view_teacher_calendar_denied_for_other_calendar(): void
    {
        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('view-teacher-calendar', $otherTeacher));
    }

    public function test_view_teacher_calendar_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('calendars.view');
        $teacher = Teacher::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-teacher-calendar', $teacher));
    }

    // ── view-room-calendar ──

    public function test_view_room_calendar_allowed_for_teacher_when_config_enabled(): void
    {
        $teacher = Teacher::factory()->create();
        config()->set('settings.teachers_can_view_calendars', true);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('view-room-calendar'));
    }

    public function test_view_room_calendar_denied_for_teacher_when_config_disabled(): void
    {
        $teacher = Teacher::factory()->create();
        config()->set('settings.teachers_can_view_calendars', false);

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('view-room-calendar'));
    }

    public function test_view_room_calendar_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('calendars.view');
        config()->set('settings.teachers_can_view_calendars', false);

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-room-calendar'));
    }

    // ── view-course ──

    public function test_view_course_allowed_for_course_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('view-course', $course));
    }

    public function test_view_course_denied_for_other_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('view-course', $course));
    }

    public function test_view_course_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('courses.view');
        $course = Course::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-course', $course));
    }

    // ── view-enrollment ──

    public function test_view_enrollment_allowed_for_enrolled_student(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->actingAs($student->user);
        $this->assertTrue(Gate::allows('view-enrollment', $enrollment));
    }

    public function test_view_enrollment_allowed_for_course_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $student = Student::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('view-enrollment', $enrollment));
    }

    public function test_view_enrollment_denied_for_other_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();
        $student = Student::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('view-enrollment', $enrollment));
    }

    public function test_view_enrollment_denied_for_other_student(): void
    {
        $student = Student::factory()->create();
        $otherStudent = Student::factory()->create();
        $course = Course::factory()->create();
        $enrollment = Enrollment::create([
            'student_id' => $otherStudent->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->actingAs($student->user);
        $this->assertFalse(Gate::allows('view-enrollment', $enrollment));
    }

    public function test_view_enrollment_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('evaluation.view');
        $student = Student::factory()->create();
        $course = Course::factory()->create();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-enrollment', $enrollment));
    }

    // ── enroll-in-course ──

    public function test_enroll_in_course_allowed_for_course_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('enroll-in-course', $course));
    }

    public function test_enroll_in_course_denied_without_permission(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $this->actingAs($user);
        $this->assertFalse(Gate::allows('enroll-in-course', $course));
    }

    public function test_enroll_in_course_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('enrollments.edit');
        $course = Course::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('enroll-in-course', $course));
    }

    // ── enroll-students ──

    public function test_enroll_students_allowed_for_teachers(): void
    {
        $teacher = Teacher::factory()->create();

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('enroll-students'));
    }

    public function test_enroll_students_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('enrollments.edit');

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('enroll-students'));
    }

    // ── view-teacher-hours ──

    public function test_view_teacher_hours_allowed_for_own(): void
    {
        $teacher = Teacher::factory()->create();

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('view-teacher-hours', $teacher));
    }

    public function test_view_teacher_hours_denied_for_other(): void
    {
        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('view-teacher-hours', $otherTeacher));
    }

    public function test_view_teacher_hours_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('hr.view');
        $teacher = Teacher::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('view-teacher-hours', $teacher));
    }

    // ── edit-result ──

    public function test_edit_result_allowed_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('evaluation.edit');
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Gate::allows('edit-result', $enrollment));
    }

    public function test_edit_result_allowed_for_teacher_when_config_enabled(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $enrollment = Enrollment::factory()->create(['course_id' => $course->id]);
        config()->set('settings.teachers_can_edit_result', true);

        $this->actingAs($teacher->user);
        $this->assertTrue(Gate::allows('edit-result', $enrollment));
    }

    public function test_edit_result_denied_for_teacher_when_config_disabled(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $enrollment = Enrollment::factory()->create(['course_id' => $course->id]);
        config()->set('settings.teachers_can_edit_result', false);

        $this->actingAs($teacher->user);
        $this->assertFalse(Gate::allows('edit-result', $enrollment));
    }

    public function test_edit_result_denied_without_permission(): void
    {
        $user = User::factory()->create();
        $enrollment = Enrollment::factory()->create();
        config()->set('settings.teachers_can_edit_result', false);

        $this->actingAs($user);
        $this->assertFalse(Gate::allows('edit-result', $enrollment));
    }
}
