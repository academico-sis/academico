<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\GradeType;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GradesTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
    }

    public function test_adding_a_new_gradetype_to_course()
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
