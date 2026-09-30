<?php

namespace Tests\Feature;

use App\Filament\Pages\HrDashboard;
use App\Models\Config;
use App\Models\Course;
use App\Models\Event;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Period;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class HrDashboardTest extends TestCase
{
    use RefreshDatabase;

    private Period $period;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
        ]);

        Permission::findOrCreate('hr.view', 'web');
        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->givePermissionTo('hr.view');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $year = Year::factory()->create();
        $this->period = Period::factory()->create([
            'year_id' => $year->id,
            'start' => '2025-01-01',
            'end' => '2025-01-31',
        ]);
        Config::where('name', 'current_period')->update(['value' => $this->period->id]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_hr_dashboard_loads_with_correct_positive_hours(): void
    {
        $teacher = Teacher::factory()->create();

        Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-01-10 09:00:00',
            'end' => '2025-01-10 11:00:00',
        ]);
        Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-01-15 14:00:00',
            'end' => '2025-01-15 16:30:00',
        ]);

        $this->actingAs($this->admin);

        $component = Livewire::test(HrDashboard::class);

        $teacherRow = collect($component->get('teacherHours'))
            ->firstWhere('teacherId', $teacher->id);

        $this->assertNotNull($teacherRow);
        $this->assertEquals(4.5, $teacherRow['scheduledFaceToFace']);
        $this->assertGreaterThanOrEqual(0, $teacherRow['scheduledRemote']);
        $this->assertGreaterThan(0, $teacherRow['scheduledFaceToFace']);
    }

    public function test_hr_dashboard_leave_days_counted_within_period(): void
    {
        $teacher = Teacher::factory()->create();
        $leaveType = LeaveType::factory()->create();

        // Leave inside period
        Leave::factory()->create([
            'teacher_id' => $teacher->id,
            'date' => '2025-01-10',
            'leave_type_id' => $leaveType->id,
        ]);
        Leave::factory()->create([
            'teacher_id' => $teacher->id,
            'date' => '2025-01-15',
            'leave_type_id' => $leaveType->id,
        ]);
        // Leave outside period — should not be counted
        Leave::factory()->create([
            'teacher_id' => $teacher->id,
            'date' => '2025-03-01',
            'leave_type_id' => $leaveType->id,
        ]);

        $this->actingAs($this->admin);

        $component = Livewire::test(HrDashboard::class);

        $teacherRow = collect($component->get('teacherHours'))
            ->firstWhere('teacherId', $teacher->id);

        $this->assertNotNull($teacherRow);
        $this->assertEquals(2, $teacherRow['leaveDays']);
    }

    public function test_hr_dashboard_events_outside_period_not_counted(): void
    {
        $teacher = Teacher::factory()->create();

        // Event inside period
        Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-01-10 09:00:00',
            'end' => '2025-01-10 11:00:00',
        ]);
        // Event outside period
        Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-03-01 09:00:00',
            'end' => '2025-03-01 11:00:00',
        ]);

        $this->actingAs($this->admin);

        $component = Livewire::test(HrDashboard::class);

        $teacherRow = collect($component->get('teacherHours'))
            ->firstWhere('teacherId', $teacher->id);

        $this->assertNotNull($teacherRow);
        $this->assertEquals(2.0, $teacherRow['scheduledFaceToFace']);
    }

    public function test_hr_dashboard_updates_on_period_change(): void
    {
        $year = Year::factory()->create();
        $otherPeriod = Period::factory()->create([
            'year_id' => $year->id,
            'start' => '2025-03-01',
            'end' => '2025-03-31',
        ]);

        $teacher = Teacher::factory()->create();

        // Only has events in the other period
        Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-03-10 09:00:00',
            'end' => '2025-03-10 11:00:00',
        ]);

        $this->actingAs($this->admin);

        $component = Livewire::test(HrDashboard::class);

        // Initially (default period Jan): 0 hours for this teacher
        $rowBefore = collect($component->get('teacherHours'))
            ->firstWhere('teacherId', $teacher->id);
        $this->assertEquals(0.0, $rowBefore['scheduledFaceToFace']);

        // Switch to March period
        $component->set('selectedPeriodId', $otherPeriod->id);

        $rowAfter = collect($component->get('teacherHours'))
            ->firstWhere('teacherId', $teacher->id);
        $this->assertEquals(2.0, $rowAfter['scheduledFaceToFace']);
    }

    public function test_hr_dashboard_shows_theoretical_planned_hours_from_course_volumes(): void
    {
        $teacher = Teacher::factory()->create();

        Course::factory()->create([
            'teacher_id' => $teacher->id,
            'period_id' => $this->period->id,
            'volume' => 120,
            'remote_volume' => 30,
            'parent_course_id' => null,
        ]);

        $this->actingAs($this->admin);

        $component = Livewire::test(HrDashboard::class);

        $teacherRow = collect($component->get('teacherHours'))
            ->firstWhere('teacherId', $teacher->id);

        $this->assertNotNull($teacherRow);
        $this->assertEquals(120.0, $teacherRow['theoreticalFaceToFace']);
        $this->assertEquals(30.0, $teacherRow['theoreticalRemote']);
        $this->assertEquals(150.0, $teacherRow['theoreticalTotal']);
    }

    public function test_date_filter_hides_theoretical_columns(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(HrDashboard::class);

        // No filter by default
        $this->assertFalse($component->get('usesDateFilter'));

        // Setting a custom start date enables the filter
        $component->set('startDate', '2025-01-10');
        $this->assertTrue($component->get('usesDateFilter'));

        // Setting a custom end date also enables the filter
        $component->set('startDate', '2025-01-01'); // reset start
        $component->set('endDate', '2025-01-20');
        $this->assertTrue($component->get('usesDateFilter'));
    }

    public function test_clear_filter_restores_period_dates_and_shows_theoretical_columns(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(HrDashboard::class);

        // Apply a custom date filter
        $component->set('startDate', '2025-01-10');
        $this->assertTrue($component->get('usesDateFilter'));

        // Clear restores the flag and the period dates
        $component->call('clearDateFilter');

        $this->assertFalse($component->get('usesDateFilter'));
        $this->assertEquals('2025-01-01', $component->get('startDate'));
        $this->assertEquals('2025-01-31', $component->get('endDate'));
    }

    public function test_period_change_clears_date_filter(): void
    {
        $year = Year::factory()->create();
        $otherPeriod = Period::factory()->create([
            'year_id' => $year->id,
            'start' => '2025-03-01',
            'end' => '2025-03-31',
        ]);

        $this->actingAs($this->admin);

        $component = Livewire::test(HrDashboard::class);

        // Apply a filter, then switch period
        $component->set('startDate', '2025-01-10');
        $this->assertTrue($component->get('usesDateFilter'));

        $component->set('selectedPeriodId', $otherPeriod->id);
        $this->assertFalse($component->get('usesDateFilter'));
        $this->assertEquals('2025-03-01', $component->get('startDate'));
        $this->assertEquals('2025-03-31', $component->get('endDate'));
    }

    public function test_hr_dashboard_remote_hours_prorated_for_period_overlap(): void
    {
        $teacher = Teacher::factory()->create();

        // Course spanning a wide range with 10h remote volume over ~10 weeks
        Course::factory()->create([
            'teacher_id' => $teacher->id,
            'remote_volume' => 10,
            'start_date' => '2025-01-06',
            'end_date' => '2025-03-16',
            'parent_course_id' => null,
        ]);

        $this->actingAs($this->admin);

        $component = Livewire::test(HrDashboard::class);

        $teacherRow = collect($component->get('teacherHours'))
            ->firstWhere('teacherId', $teacher->id);

        $this->assertNotNull($teacherRow);
        $this->assertGreaterThan(0, $teacherRow['scheduledRemote']);
        // Remote hours for the period overlap should be less than total course volume
        $this->assertLessThan(10, $teacherRow['scheduledRemote']);
    }
}
