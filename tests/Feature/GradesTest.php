<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Period;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GradesTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
    }

    public function test_adding_a_new_gradetype_to_course()
    {
        $this->markTestIncomplete('Test unfinished');
    }

    /**
     * Check if a course created within the current period is visible in the course management panel.
     */
    public function test_that_authorized_user_can_view_courses_within_other_periods()
    {
        $this->markTestIncomplete('Test unfinished');
    }

    /**
     * Ensure that courses administration is not accessible to users who don't have permission.
     */
    public function test_that_unauthroized_user_cannot_view_courses()
    {
        $this->markTestIncomplete('Test unfinished');
    }
}
