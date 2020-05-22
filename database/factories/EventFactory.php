<?php

use App\Models\Course;
use App\Models\Event;
use App\Models\Room;
use App\Models\Teacher;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'teacher_id' => factory(Teacher::class)->create()->id,
        'course_id' => factory(Course::class)->create()->id,
        'room_id' => factory(Room::class)->create()->id,
        'start' => $faker->datetime(),
        'end' => $faker->datetime(),
        'name' => $faker->word,
    ];
});
