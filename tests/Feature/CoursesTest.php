<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CoursesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    /**
     * Check if a course created within the current period is visible in the course management panel.
     */
    public function test_that_authorized_user_can_view_courses_within_the_current_period()
    {
    }

    /**
     * Check if a course created within the current period is visible in the course management panel.
     */
    public function test_that_authorized_user_can_view_courses_within_other_periods()
    {
    }

    /**
     * Ensure that courses administration is not accessible to users who don't have permission.
     */
    public function test_that_unauthroized_user_cannot_view_courses()
    {
    }
}
