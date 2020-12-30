<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Grade::class, function (Faker $faker) {
    return [
        'grade_type_id' => factory(App\Models\GradeType::class),
        'student_id' => factory(App\Models\Student::class),
        'course_id' => $faker->randomNumber(),
        'grade' => $faker->randomFloat(),
        'deleted_at' => $faker->dateTime(),
    ];
});
