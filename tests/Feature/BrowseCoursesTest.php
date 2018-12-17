<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BrowseCoursesTest extends TestCase
{

    /**
     * Prerequisite for the app to work.
     * 
     * The period has been created by DB seeding.
     *
     * @return void
     */
    public function test_that_there_exists_a_current_period()
    {
        $default_period = \App\Models\Period::get_default_period();
        $this->assertTrue($default_period->exists());
    }

    /**
     * Check if a course created within the current period is visible in the course management panel.
     * 
     * The course has been created by DB seeding.
     *
     * @return void
     */
    public function test_that_a_new_course_within_the_current_period_appears_in_courses_management_panel()
    {
        // login the first fake user created by DB seeding
        $user = \App\User::find(1);
        \Auth::guard(backpack_guard_name())->login($user);

        // visit the course management page and assert that we see the title of the course
        $response = $this->get('/courses');
        $response->assertSee('PHP UNIT TEST COURSE');
    }
}
