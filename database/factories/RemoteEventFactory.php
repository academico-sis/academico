<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\RemoteEvent::class, function (Faker $faker) {
    return [
        'teacher_id' => factory(App\Models\Teacher::class),
        'name' => $faker->name(),
        'worked_hours' => $faker->randomNumber(),
        'period_id' => factory(App\Models\Period::class),
    ];
});
