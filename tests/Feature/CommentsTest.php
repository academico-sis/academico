<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('DatabaseSeeder');
    }

    /**
     * Check that an authorized user can add a comment visible to everyone.
     */
    public function test_student_comment_creation()
    {
        // act: when the teacher submits the comment form

        // assert that the comment is saved to the DB

        // and is visible on the student details screen
    }
}
