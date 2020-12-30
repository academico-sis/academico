<?php

use App\Models\Campus;
use App\Models\Course;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Room;
use App\Models\Teacher;
use Faker\Generator as Faker;
/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Course::class, function (Faker $faker) {
    return [
        'name' => 'TEST COURSE LEVEL '.$faker->randomDigit,
        'campus_id' => factory(Campus::class),
        'rhythm_id' => factory(Rhythm::class),
        'level_id' => factory(Level::class),
        'volume' => 10,
        'price' => 100,
        'start_date' => date('Y-m-d', strtotime('-10 days')),
        'end_date' => date('Y-m-d', strtotime('+30 days')),
        'room_id' => factory(Room::class),
        'teacher_id' => factory(Teacher::class),
        'parent_course_id' => null,
        'exempt_attendance' => false,
        'period_id' => Period::first(),
        'opened' => 1,
        'spots' => 10,
    ];
});
