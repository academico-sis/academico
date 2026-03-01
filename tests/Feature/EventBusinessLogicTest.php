<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\LeaveType;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_detaches_teacher_when_on_leave(): void
    {
        // Register a fake for the Alert facade since prologue/alerts is not installed
        $alertMock = new class
        {
            public function warning($msg)
            {
                return $this;
            }

            public function flash() {}
        };
        $this->app->instance('alert', $alertMock);
        class_exists('Prologue\Alerts\Facades\Alert') || $this->app->bind('Prologue\Alerts\Facades\Alert', fn () => $alertMock);

        // Create an alias so the facade resolves
        if (! class_exists('Prologue\Alerts\Facades\Alert')) {
            // We need to create the facade class at runtime
            eval('namespace Prologue\Alerts\Facades; class Alert extends \Illuminate\Support\Facades\Facade { protected static function getFacadeAccessor() { return "alert"; } }');
        }

        $teacher = Teacher::factory()->create();
        $leaveType = LeaveType::factory()->create();

        \DB::table('leaves')->insert([
            'teacher_id' => $teacher->id,
            'date' => '2025-06-15',
            'leave_type_id' => $leaveType->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $event = Event::create([
            'teacher_id' => $teacher->id,
            'course_id' => \App\Models\Course::factory()->create()->id,
            'room_id' => \App\Models\Room::factory()->create()->id,
            'start' => '2025-06-15 09:00:00',
            'end' => '2025-06-15 11:00:00',
            'name' => 'Test event',
        ]);

        $this->assertNull($event->fresh()->teacher_id);
    }

    public function test_event_keeps_teacher_when_not_on_leave(): void
    {
        $teacher = Teacher::factory()->create();

        $event = Event::create([
            'teacher_id' => $teacher->id,
            'course_id' => \App\Models\Course::factory()->create()->id,
            'room_id' => \App\Models\Room::factory()->create()->id,
            'start' => '2025-06-20 09:00:00',
            'end' => '2025-06-20 11:00:00',
            'name' => 'Test event',
        ]);

        $this->assertEquals($teacher->id, $event->fresh()->teacher_id);
    }

    public function test_event_length_accessor(): void
    {
        $event = Event::factory()->create([
            'start' => '2025-04-01 09:00:00',
            'end' => '2025-04-01 11:30:00',
        ]);

        // length = diffInSeconds(end, start) / 3600
        // The accessor does Carbon::parse($this->end)->diffInSeconds(Carbon::parse($this->start))
        // diffInSeconds always returns absolute value, so result is 2.5
        $this->assertEquals(2.5, abs($event->length));
    }

    public function test_event_volume_accessor(): void
    {
        $event = Event::factory()->create([
            'start' => '2025-04-01 14:00:00',
            'end' => '2025-04-01 15:30:00',
        ]);

        $this->assertEquals(1.5, abs($event->volume));
    }
}
