<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\CourseTime::class, function (Faker $faker) {
    return [
        'course_id' => factory(App\Models\Course::class),
        'day' => $faker->numberBetween(0, 6),
        'start' => $faker->time(),
        'end' => $faker->time(),
    ];
});
