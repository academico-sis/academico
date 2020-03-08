<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ChildCoursesTest extends TestCase
{
    use RefreshDatabase;

    public function test_that_an_enrollment_in_a_course_with_children_also_create_child_enrollments()
    {
        
    }

    public function test_that_billing_a_parent_enrollment_also_bills_child_enrollments()
    {
        
    }

    public function test_that_a_parent_enrollment_only_appears_once_in_students_lists()
    {
        // and counts like one in the reports
    }
}
