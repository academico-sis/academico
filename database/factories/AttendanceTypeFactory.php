<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\AttendanceType::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
    ];
});
