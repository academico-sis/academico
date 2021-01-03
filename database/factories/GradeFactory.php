<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Grade::class, function (Faker $faker) {
    return [
        'grade_type_id' => factory(App\Models\GradeType::class),
        'enrollment_id' => factory(App\Models\Enrollment::class),
        'grade' => $faker->randomFloat(),
        'deleted_at' => $faker->dateTime(),
    ];
});
