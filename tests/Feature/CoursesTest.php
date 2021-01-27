<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class CoursesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->setSharedVariables();
        $this->seed('TestSeeder');
    }

    /** @test */
    public function admin_can_see_course_list()
    {
        $user = factory(User::class)->create();
        $user->assignRole('admin');
        \Auth::guard(backpack_guard_name())->login($user);

        $response = $this->get(route('get-courses-list'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('courses.list');
        $response->assertViewHas('defaultPeriod');
        $response->assertViewHas('isAllowedToEdit');
        $response->assertViewHas('rhythms');
        $response->assertViewHas('levels');
    }

    /**
     * Check if a course created within the current period is visible in the course management panel.
     */
    public function test_that_authorized_user_can_view_courses_within_the_current_period()
    {
        $currentPeriod = factory(Period::class)->create();
        $anotherPeriod = factory(Period::class)->create();
        $courseInCurrentPeriod = factory(Course::class)->create([
            'name' => 'courseInCurrentPeriod',
            'campus_id' => 1,
            'period_id' => $currentPeriod->id,
        ]);
        $courseInAnotherPeriod = factory(Course::class)->create([
            'name' => 'courseInAnotherPeriod',
            'campus_id' => 1,
            'period_id' => $anotherPeriod->id,
        ]);

        $user = factory(User::class)->create();
        $user->assignRole('admin');

        \Auth::guard(backpack_guard_name())->login($user);
        $response = $this->get(route('search-courses', [
            'filter[period_id]' => $currentPeriod->id,
        ]));

        $response->assertSee($courseInCurrentPeriod->id);
        $response->assertSee($courseInCurrentPeriod->name);
        $response->assertDontSee($courseInAnotherPeriod);
        $response->assertDontSee($courseInAnotherPeriod->name);
    }

    /**
     * Ensure that courses administration is not accessible to users who don't have permission.
     */
    public function test_that_unauthroized_user_cannot_view_courses()
    {
        $period = factory(Period::class)->create();
        factory(Course::class)->create([
            'campus_id' => 1,
            'period_id' => $period->id,
        ]);

        // User without special role
        $user = factory(User::class)->create();

        \Auth::guard(backpack_guard_name())->login($user);
        $response = $this->get(route('search-courses', [
            'filter[period_id]' => $period->id,
        ]));

        $response->assertForbidden();
    }

    /** @test */
    public function a_coursetime_can_be_added_to_an_existing_course()
    {
        $this->logAdmin();

        // given a course
        $course = factory(Course::class)->create();

        $this->assertEquals(0, $course->times()->count());

        // some coursetimes can be added
        $response = $this->putJson(route('course.update', ['id' => $course->id]), [
            'campus_id' => $course->campus_id,
            'rhythm_id' => $course->rhythm_id,
            'level_id' => $course->level_id,
            'name' => $course->name,
            'price' => $course->price,
            'volume' => $course->volume,
            'spots' => $course->spots,
            'exempt_attendance' => $course->exempt_attendance,
            'teacher_id' => $course->teacher_id,
            'room_id' => $course->room_id,
            'period_id' => $course->period_id,
            'start_date' => $course->start_date,
            'end_date' => $course->end_date,
            'times' => '[{"day":"1","start":"11:00:00","end":"12:00:00"},{"day":"5","start":"13:00:00","end":"15:00:00"}]',
        ]);

        $this->assertEquals(2, $course->fresh()->times()->count());
    }
}
