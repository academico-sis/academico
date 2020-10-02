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
            'campus_id' => 1,
            'period_id' => $currentPeriod->id,
        ]);
        $courseInAnotherPeriod = factory(Course::class)->create([
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
}
