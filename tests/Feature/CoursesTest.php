<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Period;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CoursesTest extends TestCase
{
    use RefreshDatabase;

    use DatabaseMigrations;

    /**
     * Check if a course created within the current period is visible in the course management panel.
     */
    public function test_that_authorized_user_can_view_courses_within_the_current_period()
    {
        $this->seed('DatabaseSeeder');

        $period = factory(Period::class)->create();

        // create a fake user
        $user = factory(User::class)->create();
        
        // give them permission to manage courses
        $user->givePermissionTo('courses.view');
        
        // log them in
        \Auth::guard(backpack_guard_name())->login($user);

        // create a fake course
        $course = factory(Course::class)->create([
            'period_id' => $period->id,
        ]);

        // visit the course management page and assert that we see the title of the course
        $response = $this->get("/admin/course/");
        $response->assertSee($course->name);
    }

    /**
     * Check if a course created within the current period is visible in the course management panel.
     */
    public function test_that_authorized_user_can_view_courses_within_other_periods()
    {
        
    }

    /**
     * Ensure that courses administration is not accessible to users who don't have permission
     */
    public function test_that_unauthroized_user_cannot_view_courses()
    {

    }
}
