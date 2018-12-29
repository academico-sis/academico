<?php

use Faker\Generator as Faker;


$factory->define(\App\Models\Course::class, function (Faker $faker) {
    $period = \App\Models\Period::get_default_period();

    return [
        'name' => "PHP UNIT TEST COURSE",
        'campus_id' => 1,
        'rythm_id' => 1,
        'level_id' => 1,
        'volume' => 10,
        'price' => 10,
        'start_date' => date('Y-m-d', strtotime("-12 days")),
        'end_date' => date('Y-m-d', strtotime("+22 days")),
        'room_id' => 1,
        'teacher_id' => 1,
        'parent_course_id' => null,
        'eval_type' => null,
        'exempt_attendance' => 1,
        'period_id' => $period->id,
        'opened' => 1,
        'spots' => 10
    ];
});
