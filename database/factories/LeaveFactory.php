<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Leave::class, function (Faker $faker) {
    return [
        'teacher_id' => factory(App\Models\Teacher::class),
        'date' => $faker->date(),
        'leave_type_id' => factory(App\Models\LeaveType::class),
    ];
});
