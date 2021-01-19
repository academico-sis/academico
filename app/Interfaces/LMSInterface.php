<?php

namespace App\Interfaces;

use App\Models\Course;
use App\Models\Level;
use App\Models\Rhythm;
use App\Models\Student;
use App\Models\User;

interface LMSInterface
{
    public function authenticate() : string;

    public function createUser(User $user) : void;

    //public function updateUser(User $user) : void;

    public function createCourse(Course $course) : void;

    public function updateCourse(Course $course) : void;

    public function enrollStudent(Course $course, Student $student) : void;
}
