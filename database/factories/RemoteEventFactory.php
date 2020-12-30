<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\RemoteEvent::class, function (Faker $faker) {
    return [
        'teacher_id' => factory(App\Models\Teacher::class),
        'name' => $faker->name,
        'worked_hours' => $faker->randomNumber(),
        'period_id' => factory(App\Models\Period::class),
    ];
});
