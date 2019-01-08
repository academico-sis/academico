<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AttendanceTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_a_teacher_can_register_attendance_only_for_their_own_classes()
    {
        $this->seed('DatabaseSeeder');
        
        // given a course with a teacher and a student
        $teacher = factory(User::class)->create();
        $teacher->assignRole('teacher');
        
        $course = factory(Course::class)->create([
            'teacher_id' => $teacher->id,
        ]);
        
        $student = factory(User::class)->create();
        $student->assignRole('student');
       
        $student->enroll($course);

        \Auth::guard(backpack_guard_name())->login($teacher);

        // the teacher can register attendance for the student for their classes

        // but not for classes assigned to other teachers
    }
}
