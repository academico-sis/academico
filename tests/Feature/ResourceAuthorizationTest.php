<?php

namespace Tests\Feature;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Leaves\LeaveResource;
use App\Filament\Resources\Periods\PeriodResource;
use App\Filament\Resources\Professions\ProfessionResource;
use App\Filament\Resources\Settings\Fees\FeeResource;
use App\Filament\Resources\Settings\Rooms\RoomResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Teachers\TeacherResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Without model policies, Filament allows every operation to anyone who can access
 * a resource: the write operations must be restricted by the matching permissions.
 */
class ResourceAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
        ]);

        $this->seed(PermissionsSeeder::class);
    }

    private function actingAsRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);

        return $user;
    }

    public function test_secretary_can_view_but_not_change_courses(): void
    {
        $this->actingAsRole('secretary');
        $course = Course::factory()->create();

        $this->assertTrue(CourseResource::canAccess());
        $this->assertFalse(CourseResource::canCreate());
        $this->assertFalse(CourseResource::canEdit($course));
        $this->assertFalse(CourseResource::canDelete($course));
        $this->assertFalse(CourseResource::canDeleteAny());
        $this->assertFalse(CourseResource::canReplicate($course));

        $this->get(CourseResource::getUrl('create'))->assertForbidden();
        $this->get(CourseResource::getUrl('edit', ['record' => $course]))->assertForbidden();
    }

    public function test_secretary_cannot_change_course_related_records(): void
    {
        $this->actingAsRole('secretary');

        $this->assertFalse(EventResource::canCreate());
        $this->assertFalse(EventResource::canEdit(Event::factory()->create()));
        $this->assertFalse(PeriodResource::canCreate());
        $this->assertFalse(PeriodResource::canDelete(Period::factory()->create()));
    }

    public function test_secretary_can_edit_but_not_delete_enrollments_and_students(): void
    {
        $this->actingAsRole('secretary');
        $student = Student::factory()->create();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => Course::factory()->create()->id,
            'status_id' => 1,
        ]);

        $this->assertTrue(EnrollmentResource::canEdit($enrollment));
        $this->assertFalse(EnrollmentResource::canDelete($enrollment));
        $this->assertFalse(EnrollmentResource::canDeleteAny());

        $this->assertTrue(StudentResource::canCreate());
        $this->assertTrue(StudentResource::canEdit($student));
        $this->assertFalse(StudentResource::canDelete($student));
    }

    public function test_hr_records_require_hr_manage(): void
    {
        $user = $this->actingAsRole('viewer');
        $user->givePermissionTo('hr.view');
        $teacher = Teacher::factory()->create();

        $this->assertTrue(TeacherResource::canAccess());
        $this->assertFalse(TeacherResource::canCreate());
        $this->assertFalse(TeacherResource::canEdit($teacher));
        $this->assertFalse(TeacherResource::canDelete($teacher));
        $this->assertFalse(LeaveResource::canCreate());

        $user->givePermissionTo('hr.manage');

        $this->assertTrue(TeacherResource::canEdit($teacher));
        $this->assertTrue(LeaveResource::canCreate());
    }

    public function test_admin_keeps_full_access(): void
    {
        $this->actingAsRole('admin');
        $course = Course::factory()->create();

        $this->assertTrue(CourseResource::canCreate());
        $this->assertTrue(CourseResource::canEdit($course));
        $this->assertTrue(CourseResource::canDelete($course));
        $this->assertTrue(EnrollmentResource::canDeleteAny());
        $this->assertTrue(TeacherResource::canCreate());

        $this->get(CourseResource::getUrl('edit', ['record' => $course]))->assertSuccessful();
        $this->get(RoomResource::getUrl('index'))->assertSuccessful();
    }

    public function test_settings_resources_are_restricted_to_admins(): void
    {
        $this->actingAsRole('secretary');

        foreach ([RoomResource::class, FeeResource::class, ProfessionResource::class] as $resource) {
            $this->assertFalse($resource::canAccess(), $resource);
            $this->assertFalse($resource::canCreate(), $resource);
            $this->get($resource::getUrl('index'))->assertForbidden();
        }
    }

    public function test_teachers_cannot_reach_settings_resources(): void
    {
        $this->actingAs(Teacher::factory()->create()->user);

        $this->assertFalse(FeeResource::canAccess());
        $this->get(FeeResource::getUrl('index'))->assertForbidden();
    }
}
