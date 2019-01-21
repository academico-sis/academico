<?php

use App\Models\Course;
use App\Models\Period;
use Faker\Generator as Faker;


$factory->define(Course::class, function (Faker $faker) {

    return [
        'name' => "PHP UNIT TEST COURSE",
        'campus_id' => 1,
        'rhythm_id' => 1,
        'level_id' => 1,
        'volume' => 10,
        'price' => 10,
        'start_date' => date('Y-m-d', strtotime("-12 days")),
        'end_date' => date('Y-m-d', strtotime("+22 days")),
        'room_id' => 1,
        'teacher_id' => 1,
        'parent_course_id' => null,
        'exempt_attendance' => false,
        'period_id' => 1,
        'opened' => 1,
        'spots' => 10
    ];
});
