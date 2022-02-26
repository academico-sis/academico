<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Attendance::class, function (Faker $faker) {
    return [
        'student_id' => factory(App\Models\Student::class),
        'event_id' => factory(App\Models\Event::class),
        'attendance_type_id' => factory(App\Models\AttendanceType::class),
    ];
});
