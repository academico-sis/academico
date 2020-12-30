<?php

use App\Models\Year;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Year::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->year($max = 'now'),
    ];
});
