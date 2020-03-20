<?php

use App\Models\Course;
use App\Models\Event;
use App\Models\Room;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'teacher_id' => factory(Teacher::class)->create()->id,
        'course_id' => factory(Course::class)->create()->id,
        'room_id' => factory(Room::class)->create()->id,
        'start' => '2019-01-01 18:00:00',
        'end' => '2019-01-01 18:00:00',
        'name' => 'test event',
    ];
});
