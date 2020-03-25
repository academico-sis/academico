<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Faker\Generator as Faker;

$factory->define(Enrollment::class, function (Faker $faker) {
    return [
        'student_id' => factory(Student::class)->create()->id,
        'course_id'  => factory(Course::class)->create()->id,
        'status_id'  => 1,
    ];
});
