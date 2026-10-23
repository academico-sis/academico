<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_unique_regardless_of_case(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $this->expectException(UniqueConstraintViolationException::class);

        User::factory()->create(['email' => 'Jane@Example.com']);
    }

    public function test_users_without_email_do_not_conflict(): void
    {
        $users = collect(['', '  ', null])->map(fn (?string $email): User => User::factory()->create(['email' => $email]));

        $this->assertSame([null, null, null], $users->map(fn (User $user): ?string => $user->fresh()->email)->all());
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
        $user = User::factory()->create(['email' => ' John.Doe@Example.COM ']);

        $this->assertEquals('john.doe@example.com', $user->email);
    }
}
