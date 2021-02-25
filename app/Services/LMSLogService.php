<?php


namespace App\Services;


use App\Interfaces\LMSInterface;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;

class LMSLogService implements LMSInterface
{

    public function authenticate(): ?string
    {
        return null;
    }

    public function createUser(User $user, ?string $password): void
    {
        // TODO: Implement createUser() method.
    }

    public function updateUser(User $user, ?string $password): void
    {
        // TODO: Implement updateUser() method.
    }

    public function createCourse(Course $course): void
    {
        // TODO: Implement createCourse() method.
    }

    public function updateCourse(Course $course): void
    {
        // TODO: Implement updateCourse() method.
    }

    public function enrollStudent(Course $course, Student $student): void
    {
        // TODO: Implement enrollStudent() method.
    }

    public function removeStudent($courseId, $userId): void
    {
        // TODO: Implement removeStudent() method.
    }
}
