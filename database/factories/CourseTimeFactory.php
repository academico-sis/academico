<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\CourseTime::class, function (Faker $faker) {
    return [
        'course_id' => factory(App\Models\Course::class),
        'day' => $faker->randomNumber(1,6),
        'start' => $faker->time(),
        'end' => $faker->time(),
    ];
});
