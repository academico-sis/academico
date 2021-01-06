<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\CourseTime::class, function (Faker $faker) {
    return [
        'course_id' => factory(App\Models\Course::class),
        'day' => $faker->numberBetween(0, 6),
        'start' => $faker->time(),
        'end' => $faker->time(),
    ];
});
