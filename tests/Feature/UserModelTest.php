<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_unique_username_adds_suffix_for_homonyms(): void
    {
        $this->assertEquals('janedoe', User::generateUniqueUsername('Jane', 'Doe'));

        User::factory()->create(['username' => 'janedoe']);
        User::factory()->create(['username' => 'janedoe2'])->delete();

        $this->assertEquals('janedoe3', User::generateUniqueUsername('Jane', 'Doe'));
    }

    public function test_generate_unique_username_falls_back_when_name_has_no_slug(): void
    {
        $this->assertEquals('user', User::generateUniqueUsername('', ''));
    }

    public function test_is_teacher_returns_true_when_teacher_exists(): void
    {
        $teacher = Teacher::factory()->create();

        $this->assertTrue($teacher->user->isTeacher());
    }

    public function test_is_teacher_returns_false_when_no_teacher(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isTeacher());
    }

    public function test_is_student_returns_true_when_student_exists(): void
    {
        $student = Student::factory()->create();

        $this->assertTrue($student->user->isStudent());
    }

    public function test_is_student_returns_false_when_no_student(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isStudent());
    }

    public function test_firstname_accessor_applies_title_case(): void
    {
        $user = User::factory()->create(['firstname' => 'john']);

        $this->assertEquals('John', $user->firstname);
    }

    public function test_lastname_accessor_applies_upper_case(): void
    {
        $user = User::factory()->create(['lastname' => 'smith']);

        $this->assertEquals('SMITH', $user->lastname);
    }

    public function test_email_mutator_lowercases(): void
    {
        $user = User::factory()->create(['email' => 'John.Doe@Example.COM']);

        $this->assertEquals('john.doe@example.com', $user->email);
    }
}
