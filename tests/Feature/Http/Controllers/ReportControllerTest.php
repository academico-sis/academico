<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Course;
use App\Models\Rhythm;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ReportController
 */
class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setSharedVariables();
        $this->seed('TestSeeder');
    }

    /**
     * @test
     */
    public function courses_returns_an_ok_response()
    {
        $this->logAdmin();

        $response = $this->get(route('courseReport'));

        $response->assertOk();
        $response->assertViewIs('reports.courses');
        $response->assertViewHas('selected_period');
        $response->assertViewHas('courses');

        // TODO - we could use some more in-depth assertions here.
        // create a course and check the number of enrollments
        $course = factory(Course::class)->create();
        for ($i = 1; $i <= 3; $i++) {
            $student = factory(Student::class)->create();
            $student->enroll($course);
        }

        $response = $this->get(route('courseReport', ['period', $course->period_id]));
        $response->assertSee($course->name);
        $response->assertSee(3);
    }

    /**
     * @test
     */
    public function levels_returns_an_ok_response()
    {
        $this->logAdmin();

        $response = $this->get(route('levelReport'));

        $response->assertOk();
        $response->assertViewIs('reports.levels');
        $response->assertViewHas('selected_period');
        $response->assertViewHas('data');

        // TODO complete this
    }

    /**
     * @test
     */
    public function internal_returns_an_ok_response()
    {
        $this->logAdmin();

        $response = $this->get(route('homeReport'));

        $response->assertOk();
        $response->assertViewIs('reports.internal');
        $response->assertViewHas('selected_period');
        $response->assertViewHas('pending_enrollment_count');
        $response->assertViewHas('paid_enrollment_count');
        $response->assertViewHas('total_enrollment_count');
        $response->assertViewHas('students_count');
        $response->assertViewHas('data');
        $response->assertViewHas('years');

        // TODO complete this
    }

    /**
     * @test
     */
    public function rhythms_returns_an_ok_response()
    {
        $this->logAdmin();

        $response = $this->get(route('rhythmReport'));
        $response->assertOk();
        $response->assertViewIs('reports.rhythms');
        $response->assertViewHas('selected_period');
        $response->assertViewHas('data');

        // TODO: perform additional assertions

        // rhythm 1 has 1 course and 3 enrollments
        $rhythm1 = factory(Rhythm::class)->create();
        $course1 = factory(Course::class)->create(['rhythm_id' => $rhythm1->id]);
        for ($i = 1; $i <= 3; $i++) {
            $student = factory(Student::class)->create();
            $student->enroll($course1);
        }

        // rhythm 2 has 2 course and 4 enrollments
        $rhythm2 = factory(Rhythm::class)->create();
        $course2 = factory(Course::class)->create(['rhythm_id' => $rhythm2->id]);
        for ($i = 1; $i <= 4; $i++) {
            $student = factory(Student::class)->create();
            $student->enroll($course2);
        }
        $response = $this->get(route('courseReport', ['period', $course2->period_id]));
        $response->assertSee($course2->name);
        $response->assertSee(4);
    }
}
