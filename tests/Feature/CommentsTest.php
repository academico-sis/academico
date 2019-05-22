<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Models\User;
use App\Models\Course;

class CommentsTest extends TestCase
{

    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed('DatabaseSeeder');

         // Arrange a course with a teacher and a student
         $this->teacher = factory(User::class)->create();
         $this->teacher->assignRole('teacher');
         $this->teacher->givePermissionTo('enrollments.view');
 
         $this->course = factory(Course::class)->create([
             'teacher_id' => $this->teacher->id,
         ]);
 
         $this->student = factory(User::class)->create();
         $this->student->assignRole('student');
         $this->student->enroll($this->course);
         
         \Auth::guard(backpack_guard_name())->login($this->teacher);

    }

    /**
     * Check that an authorized user can add a comment visible to everyone
     */
    public function test_public_comment_creation()
    {
        // act: when the teacher submits the comment form
        $response = $this->json('POST', "/comment", [
            'comment' => 'My test comment',
            'student_id' => $this->student->id,
            'private' => false,
        ]);

        // assert that the comment is saved to the DB
        $this->assertTrue($this->student->comments->contains('body', 'My test comment'));

        // todo assert that the student can see the comment
    }

    /**
     * Check that an authorized user can add a private comment
     */
    public function test_private_comment_creation()
    {
        
    }

}
