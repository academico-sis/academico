<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AttendanceController
 */
class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->get(route('monitorAttendance'));

        $response->assertOk();
        $response->assertViewIs('attendance.monitor');
        $response->assertViewHas('absences');
        $response->assertViewHas('courses');
        $response->assertViewHas('selected_period');
        $response->assertViewHas('isadmin');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_course_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $course = factory(\App\Models\Course::class)->create();

        $response = $this->get(route('monitorCourseAttendance', [$course]));

        $response->assertRedirect(back());

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_event_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $event = factory(\App\Models\Event::class)->create();

        $response = $this->get(route('eventAttendance', [$event]));

        $response->assertOk();
        $response->assertViewIs('attendance/event');
        $response->assertViewHas('attendances');
        $response->assertViewHas('event');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_student_attendance_for_course_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $student = factory(\App\Models\Student::class)->create();

        $response = $this->get(route('studentAttendance', [$student]));

        $response->assertOk();
        $response->assertViewIs('attendance.student');
        $response->assertViewHas('student');
        $response->assertViewHas('selectedCourse');
        $response->assertViewHas('studentEnrollments');
        $response->assertViewHas('attendances');
        $response->assertViewHas('attendanceratio');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->post(route('storeAttendance'), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function toggle_course_attendance_status_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $course = factory(\App\Models\Course::class)->create();

        $response = $this->post(route('toggleCourseAttendance', [$course]), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function toggle_event_attendance_status_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $event = factory(\App\Models\Event::class)->create();

        $response = $this->post(route('toggleEventAttendance', [$event]), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
