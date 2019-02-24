<?php

use App\Models\Year;
use Faker\Generator as Faker;

$factory->define(Year::class, function (Faker $faker) {
    return [
        'name' => $faker->year($max = 'now'),
    ];
});