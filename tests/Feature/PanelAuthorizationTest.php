<?php

namespace Tests\Feature;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Filament\Pages\CheckoutPage;
use App\Filament\Pages\CoursesReport;
use App\Filament\Pages\HrDashboard;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Filament\Resources\Leaves\LeaveResource;
use App\Filament\Resources\Periods\PeriodResource;
use App\Filament\Resources\Results\ResultResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Teachers\TeacherResource;
use App\Models\Teacher;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'courses.view', 'courses.edit', 'courses.delete',
            'enrollments.view', 'enrollments.edit', 'enrollments.delete',
            'attendance.view', 'attendance.edit',
            'evaluation.edit', 'evaluation.view',
            'reports.view', 'calendars.view',
            'hr.view', 'hr.manage',
            'student.edit', 'comments.edit', 'leads.manage',
        ] as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        $adminRole = Role::create(['name' => 'admin']);
        foreach (Permission::all() as $permission) {
            $adminRole->givePermissionTo($permission->name);
        }

        $secretaryRole = Role::create(['name' => 'secretary']);
        $secretaryRole->givePermissionTo('calendars.view');
        $secretaryRole->givePermissionTo('evaluation.view');
        $secretaryRole->givePermissionTo('attendance.view');
        $secretaryRole->givePermissionTo('attendance.edit');
        $secretaryRole->givePermissionTo('enrollments.view');
        $secretaryRole->givePermissionTo('enrollments.edit');
        $secretaryRole->givePermissionTo('courses.view');
        $secretaryRole->givePermissionTo('leads.manage');

        Role::create(['name' => 'viewer']);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    // ── Panel access ──

    public function test_admin_can_access_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->canAccessPanel(Filament::getCurrentOrDefaultPanel()));
    }

    public function test_secretary_can_access_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('secretary');

        $this->assertTrue($user->canAccessPanel(Filament::getCurrentOrDefaultPanel()));
    }

    public function test_viewer_can_access_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('viewer');

        $this->assertTrue($user->canAccessPanel(Filament::getCurrentOrDefaultPanel()));
    }

    public function test_teacher_can_access_panel(): void
    {
        $teacher = Teacher::factory()->create();

        $this->assertTrue($teacher->user->canAccessPanel(Filament::getCurrentOrDefaultPanel()));
    }

    public function test_user_without_role_cannot_access_panel(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->canAccessPanel(Filament::getCurrentOrDefaultPanel()));
    }

    // ── Resource access for admin ──

    public function test_admin_can_access_all_resources(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user);

        $this->assertTrue(CourseResource::canAccess());
        $this->assertTrue(EnrollmentResource::canAccess());
        $this->assertTrue(StudentResource::canAccess());
        $this->assertTrue(TeacherResource::canAccess());
        $this->assertTrue(PeriodResource::canAccess());
        $this->assertTrue(ResultResource::canAccess());
        $this->assertTrue(LeaveResource::canAccess());
        $this->assertTrue(SettingsCluster::canAccess());
    }

    // ── Resource access for secretary ──

    public function test_secretary_can_access_permitted_resources(): void
    {
        $user = User::factory()->create();
        $user->assignRole('secretary');

        $this->actingAs($user);

        $this->assertTrue(CourseResource::canAccess());
        $this->assertTrue(EnrollmentResource::canAccess());
        $this->assertTrue(StudentResource::canAccess());
        $this->assertTrue(PeriodResource::canAccess());
        $this->assertTrue(ResultResource::canAccess());
    }

    public function test_secretary_cannot_access_admin_only_resources(): void
    {
        $user = User::factory()->create();
        $user->assignRole('secretary');

        $this->actingAs($user);

        $this->assertFalse(TeacherResource::canAccess());
        $this->assertFalse(LeaveResource::canAccess());
        $this->assertFalse(SettingsCluster::canAccess());
    }

    // ── Resource access for viewer ──

    public function test_viewer_cannot_access_any_resources(): void
    {
        $user = User::factory()->create();
        $user->assignRole('viewer');

        $this->actingAs($user);

        $this->assertFalse(CourseResource::canAccess());
        $this->assertFalse(EnrollmentResource::canAccess());
        $this->assertFalse(StudentResource::canAccess());
        $this->assertFalse(TeacherResource::canAccess());
        $this->assertFalse(PeriodResource::canAccess());
        $this->assertFalse(ResultResource::canAccess());
        $this->assertFalse(LeaveResource::canAccess());
        $this->assertFalse(SettingsCluster::canAccess());
    }

    // ── Resource access for teacher ──

    public function test_teacher_cannot_access_resources(): void
    {
        $teacher = Teacher::factory()->create();

        $this->actingAs($teacher->user);

        $this->assertFalse(CourseResource::canAccess());
        $this->assertFalse(EnrollmentResource::canAccess());
        $this->assertFalse(StudentResource::canAccess());
        $this->assertFalse(TeacherResource::canAccess());
        $this->assertFalse(PeriodResource::canAccess());
        $this->assertFalse(ResultResource::canAccess());
        $this->assertFalse(LeaveResource::canAccess());
        $this->assertFalse(SettingsCluster::canAccess());
    }

    // ── Page access ──

    public function test_admin_can_access_report_pages(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user);

        $this->assertTrue(CoursesReport::canAccess());
        $this->assertTrue(HrDashboard::canAccess());
        $this->assertTrue(CheckoutPage::canAccess());
    }

    public function test_secretary_can_access_checkout_but_not_hr(): void
    {
        $user = User::factory()->create();
        $user->assignRole('secretary');

        $this->actingAs($user);

        $this->assertTrue(CheckoutPage::canAccess());
        $this->assertFalse(HrDashboard::canAccess());
    }

    public function test_viewer_cannot_access_pages(): void
    {
        $user = User::factory()->create();
        $user->assignRole('viewer');

        $this->actingAs($user);

        $this->assertFalse(CoursesReport::canAccess());
        $this->assertFalse(HrDashboard::canAccess());
        $this->assertFalse(CheckoutPage::canAccess());
    }
}
