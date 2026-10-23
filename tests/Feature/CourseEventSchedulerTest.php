<?php

namespace Tests\Feature;

use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseTime;
use App\Models\Event;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Room;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Course changes apply to the upcoming sessions only (or from a chosen date), past sessions are history,
 * and sessions with recorded attendance are never deleted.
 */
class CourseEventSchedulerTest extends TestCase
{
    use RefreshDatabase;

    private const PAST_WEDNESDAYS = ['2026-09-09', '2026-09-16', '2026-09-23', '2026-09-30'];

    private const UPCOMING_WEDNESDAYS = ['2026-10-07', '2026-10-14', '2026-10-21', '2026-10-28'];

    protected function setUp(): void
    {
        parent::setUp();

        // a Thursday, in the middle of the course
        $this->travelTo('2026-10-01 12:30:00');
    }

    /** From Monday 7 September to Friday 30 October, on Wednesdays from 10:00 to 12:00 */
    private function createCourse(array $attributes = []): Course
    {
        $course = Course::factory()->create(['start_date' => '2026-09-07', 'end_date' => '2026-10-30', ...$attributes]);

        // with seconds, as the database returns them (the form sends them without seconds)
        CourseTime::factory()->create(['course_id' => $course->id, 'day' => 3, 'start' => '10:00:00', 'end' => '12:00:00']);

        return $course->refresh();
    }

    /** @return array<int, string> */
    private function sessionDates(Course $course): array
    {
        return $course->events()->orderBy('start')->get()->map(fn (Event $session) => $session->start->toDateString())->all();
    }

    private function sessionOn(Course $course, string $date): Event
    {
        return $course->events()->whereDate('start', $date)->sole();
    }

    private function recordAttendance(Event $session): void
    {
        Attendance::factory()->create(['event_id' => $session->id]);
    }

    private function actingAsAdmin(): void
    {
        $this->seed(PermissionsSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    public function test_a_time_slot_creates_the_sessions_of_the_whole_course(): void
    {
        $course = $this->createCourse();

        $this->assertEquals([...self::PAST_WEDNESDAYS, ...self::UPCOMING_WEDNESDAYS], $this->sessionDates($course));
        $this->assertEquals('10:00', $this->sessionOn($course, '2026-10-07')->start->format('H:i'));
    }

    public function test_a_session_on_a_teacher_leave_day_is_created_without_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        Leave::factory()->create(['teacher_id' => $teacher->id, 'date' => '2026-10-14']);

        $course = $this->createCourse(['teacher_id' => $teacher->id]);

        $this->assertNull($this->sessionOn($course, '2026-10-14')->teacher_id);
        $this->assertEquals($teacher->id, $this->sessionOn($course, '2026-10-21')->teacher_id);
    }

    public function test_saving_the_course_form_without_changes_keeps_the_sessions_and_their_attendance(): void
    {
        $this->actingAsAdmin();
        $course = $this->createCourse();
        $this->recordAttendance($this->sessionOn($course, '2026-09-30'));
        $sessionIds = $course->events()->pluck('id')->sort()->values()->all();
        $lastSessionChange = $course->events()->max('updated_at');
        $this->travel(5)->minutes();

        Livewire::test(EditCourse::class, ['record' => $course->id])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals($sessionIds, $course->events()->pluck('id')->sort()->values()->all());
        $this->assertEquals($lastSessionChange, $course->events()->max('updated_at'));
        $this->assertEquals(1, Attendance::count());
    }

    public function test_extending_the_course_adds_sessions_and_keeps_the_existing_ones(): void
    {
        $course = $this->createCourse();
        $this->recordAttendance($this->sessionOn($course, '2026-09-30'));
        $sessionIds = $course->events()->pluck('id')->all();

        $course->update(['end_date' => '2026-11-13']);

        $this->assertEquals([...self::PAST_WEDNESDAYS, ...self::UPCOMING_WEDNESDAYS, '2026-11-04', '2026-11-11'], $this->sessionDates($course));
        $this->assertEquals(8, $course->events()->whereKey($sessionIds)->count());
        $this->assertEquals(1, Attendance::count());
    }

    public function test_extending_the_course_in_the_form_adds_sessions(): void
    {
        $this->actingAsAdmin();
        $course = $this->createCourse();

        Livewire::test(EditCourse::class, ['record' => $course->id])
            ->fillForm(['end_date' => '2026-11-13'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals([...self::PAST_WEDNESDAYS, ...self::UPCOMING_WEDNESDAYS, '2026-11-04', '2026-11-11'], $this->sessionDates($course));
    }

    public function test_shortening_the_course_deletes_the_removed_sessions_except_those_with_attendance(): void
    {
        $course = $this->createCourse();
        $this->recordAttendance($this->sessionOn($course, '2026-10-28'));

        $course->update(['end_date' => '2026-10-16']);

        $this->assertEquals([...self::PAST_WEDNESDAYS, '2026-10-07', '2026-10-14', '2026-10-28'], $this->sessionDates($course));
    }

    public function test_dates_changes_do_not_touch_past_sessions_by_default(): void
    {
        $course = $this->createCourse();

        $course->update(['start_date' => '2026-09-21']);

        $this->assertEquals([...self::PAST_WEDNESDAYS, ...self::UPCOMING_WEDNESDAYS], $this->sessionDates($course));
    }

    public function test_dates_can_be_corrected_from_an_earlier_date_in_the_course_form(): void
    {
        $this->actingAsAdmin();
        $course = $this->createCourse();
        $this->recordAttendance($this->sessionOn($course, '2026-09-09'));

        Livewire::test(EditCourse::class, ['record' => $course->id])
            ->fillForm(['start_date' => '2026-09-21', 'apply_changes_from' => '2026-09-01'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified(trans_choice(':count session is outside the course dates|:count sessions are outside the course dates', 1));

        // 16 September had no attendance: it is deleted. 9 September has attendance: it is kept.
        $this->assertEquals(['2026-09-09', '2026-09-23', '2026-09-30', ...self::UPCOMING_WEDNESDAYS], $this->sessionDates($course));
    }

    public function test_correcting_invalid_dates_creates_the_sessions(): void
    {
        $course = $this->createCourse(['start_date' => '2026-12-01', 'end_date' => '2026-09-15']);
        $this->assertEquals([], $this->sessionDates($course));

        $course->update(['start_date' => '2026-09-07', 'end_date' => '2026-10-30']);

        $this->assertEquals([...self::PAST_WEDNESDAYS, ...self::UPCOMING_WEDNESDAYS], $this->sessionDates($course));
    }

    public function test_changing_the_time_of_a_slot_moves_the_upcoming_sessions_only(): void
    {
        $course = $this->createCourse();
        $session = $this->sessionOn($course, '2026-10-07');
        $this->recordAttendance($session);

        $course->times()->first()->update(['start' => '14:00', 'end' => '16:00']);

        $this->assertEquals('10:00', $this->sessionOn($course, '2026-09-30')->start->format('H:i'));
        $this->assertEquals('14:00', $session->fresh()->start->format('H:i'));
        $this->assertEquals('16:00', $session->fresh()->end->format('H:i'));
        $this->assertEquals(1, $session->attendance()->count());
    }

    public function test_changing_the_day_of_a_slot_moves_the_upcoming_sessions_only(): void
    {
        $course = $this->createCourse();

        $course->times()->first()->update(['day' => 5]);

        $this->assertEquals([...self::PAST_WEDNESDAYS, '2026-10-02', '2026-10-09', '2026-10-16', '2026-10-23', '2026-10-30'], $this->sessionDates($course));
    }

    public function test_removing_a_slot_deletes_the_upcoming_sessions_and_keeps_the_past_ones(): void
    {
        $course = $this->createCourse();
        $this->recordAttendance($this->sessionOn($course, '2026-09-30'));

        $course->times()->first()->delete();

        $this->assertEquals(self::PAST_WEDNESDAYS, $this->sessionDates($course));
        $this->assertEquals(1, Attendance::count());
        $this->assertEquals(0, $course->events()->whereNotNull('course_time_id')->count());
    }

    public function test_a_new_teacher_takes_the_upcoming_sessions_only(): void
    {
        $previousTeacher = Teacher::factory()->create();
        $substitute = Teacher::factory()->create();
        $newTeacher = Teacher::factory()->create();
        Leave::factory()->create(['teacher_id' => $newTeacher->id, 'date' => '2026-10-28']);

        $course = $this->createCourse(['teacher_id' => $previousTeacher->id]);
        $this->sessionOn($course, '2026-10-14')->update(['teacher_id' => $substitute->id]);
        $this->sessionOn($course, '2026-10-21')->update(['teacher_id' => null]);

        $course->update(['teacher_id' => $newTeacher->id]);

        $this->assertEquals($previousTeacher->id, $this->sessionOn($course, '2026-09-30')->teacher_id);
        $this->assertEquals($newTeacher->id, $this->sessionOn($course, '2026-10-07')->teacher_id);
        $this->assertEquals($substitute->id, $this->sessionOn($course, '2026-10-14')->teacher_id);
        $this->assertEquals($newTeacher->id, $this->sessionOn($course, '2026-10-21')->teacher_id);
        $this->assertNull($this->sessionOn($course, '2026-10-28')->teacher_id);
    }

    public function test_a_teacher_change_can_apply_from_an_earlier_date_in_the_course_form(): void
    {
        $this->actingAsAdmin();
        $course = $this->createCourse();
        $newTeacher = Teacher::factory()->create();

        Livewire::test(EditCourse::class, ['record' => $course->id])
            ->fillForm(['teacher_id' => $newTeacher->id, 'apply_changes_from' => '2026-09-20'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNotEquals($newTeacher->id, $this->sessionOn($course, '2026-09-16')->teacher_id);
        $this->assertEquals($newTeacher->id, $this->sessionOn($course, '2026-09-23')->teacher_id);
    }

    public function test_a_new_room_applies_to_the_upcoming_sessions_in_the_previous_room(): void
    {
        $course = $this->createCourse();
        $previousRoom = $course->room_id;
        $otherRoom = Room::factory()->create();
        $newRoom = Room::factory()->create();
        $this->sessionOn($course, '2026-10-14')->update(['room_id' => $otherRoom->id]);

        $course->update(['room_id' => $newRoom->id]);

        $this->assertEquals($previousRoom, $this->sessionOn($course, '2026-09-30')->room_id);
        $this->assertEquals($newRoom->id, $this->sessionOn($course, '2026-10-07')->room_id);
        $this->assertEquals($otherRoom->id, $this->sessionOn($course, '2026-10-14')->room_id);
    }

    public function test_renaming_the_course_renames_the_sessions_named_after_it(): void
    {
        $course = $this->createCourse(['name' => 'French A1']);
        $this->sessionOn($course, '2026-10-14')->update(['name' => 'Exam']);

        $course->update(['name' => 'French A1+']);

        $this->assertEquals('French A1+', $this->sessionOn($course, '2026-09-30')->name);
        $this->assertEquals('Exam', $this->sessionOn($course, '2026-10-14')->name);
    }

    public function test_resync_creates_missing_sessions_and_deletes_those_outside_the_schedule(): void
    {
        $course = $this->createCourse();
        $this->sessionOn($course, '2026-10-07')->delete();
        $timeSlotId = $course->times()->first()->id;
        $extraSessions = [
            Event::factory()->create(['course_id' => $course->id, 'course_time_id' => $timeSlotId, 'start' => '2026-10-08 10:00', 'end' => '2026-10-08 12:00']),
            Event::factory()->create(['course_id' => $course->id, 'course_time_id' => $timeSlotId, 'start' => '2026-10-09 10:00', 'end' => '2026-10-09 12:00']),
        ];
        $this->recordAttendance($extraSessions[1]);

        $this->artisan('academico:resync-coursetimes', ['course_ids' => [$course->id]])->assertSuccessful();

        $this->assertEquals([...self::PAST_WEDNESDAYS, '2026-10-07', '2026-10-09', '2026-10-14', '2026-10-21', '2026-10-28'], $this->sessionDates($course));
    }

    public function test_a_leave_unassigns_the_teacher_sessions_of_that_day(): void
    {
        $course = $this->createCourse();

        Leave::factory()->create(['teacher_id' => $course->teacher_id, 'date' => '2026-10-14']);

        $this->assertNull($this->sessionOn($course, '2026-10-14')->teacher_id);
        $this->assertEquals($course->teacher_id, $this->sessionOn($course, '2026-10-21')->teacher_id);
    }

    public function test_a_leave_can_be_edited(): void
    {
        $leave = Leave::factory()->create(['date' => '2026-10-14']);
        $leaveType = LeaveType::factory()->create();

        $leave->update(['leave_type_id' => $leaveType->id]);

        $this->assertEquals($leaveType->id, $leave->fresh()->leave_type_id);
    }

    public function test_moving_a_leave_gives_the_sessions_of_the_previous_day_back(): void
    {
        $course = $this->createCourse();
        $leave = Leave::factory()->create(['teacher_id' => $course->teacher_id, 'date' => '2026-10-14']);

        $leave->update(['date' => '2026-10-21']);

        $this->assertEquals($course->teacher_id, $this->sessionOn($course, '2026-10-14')->teacher_id);
        $this->assertNull($this->sessionOn($course, '2026-10-21')->teacher_id);
    }

    public function test_deleting_a_leave_gives_the_sessions_back(): void
    {
        $course = $this->createCourse();
        $leave = Leave::factory()->create(['teacher_id' => $course->teacher_id, 'date' => '2026-10-14']);

        $leave->delete();

        $this->assertEquals($course->teacher_id, $this->sessionOn($course, '2026-10-14')->teacher_id);
    }

    public function test_the_event_form_refuses_a_teacher_on_leave(): void
    {
        $this->actingAsAdmin();
        $course = $this->createCourse();
        $session = $this->sessionOn($course, '2026-10-14');
        $substitute = Teacher::factory()->create();
        Leave::factory()->create(['teacher_id' => $substitute->id, 'date' => '2026-10-14']);

        Livewire::test(ListEvents::class)
            ->callAction(TestAction::make('edit')->table($session), data: ['teacher_id' => $substitute->id])
            ->assertHasActionErrors(['teacher_id']);

        $this->assertEquals($course->teacher_id, $session->fresh()->teacher_id);
    }
}
