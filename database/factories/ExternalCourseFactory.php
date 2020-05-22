<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ExternalCourse::class, function (Faker $faker) {
    return [
        'campus_id' => factory(App\Models\Campus::class),
        'rhythm_id' => factory(App\Models\Rhythm::class),
        'level_id' => factory(App\Models\Level::class),
        'volume' => $faker->randomNumber(),
        'name' => $faker->name,
        'price' => $faker->randomFloat(),
        'start_date' => $faker->dateTime(),
        'end_date' => $faker->dateTime(),
        'room_id' => factory(App\Models\Room::class),
        'teacher_id' => factory(App\Models\Teacher::class),
        'parent_course_id' => factory(App\Models\Course::class),
        'exempt_attendance' => $faker->boolean,
        'period_id' => factory(App\Models\Period::class),
        'opened' => $faker->boolean,
        'spots' => $faker->randomNumber(),
        'head_count' => $faker->randomNumber(),
        'new_students' => $faker->randomNumber(),
    ];
});
