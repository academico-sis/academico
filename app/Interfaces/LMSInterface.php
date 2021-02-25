<?php

namespace App\Interfaces;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;


interface LMSInterface
{
    public function authenticate() : ?string;

    public function createUser(User $user, ?string $password) : void;

    public function updateUser(User $user, ?string $password) : void;

    public function createCourse(Course $course) : void;

    public function updateCourse(Course $course) : void;

    public function enrollStudent(Course $course, Student $student) : void;

    public function removeStudent($courseId, $userId): void;
}
