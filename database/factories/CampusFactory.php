<?php

use App\Models\Campus;
use Faker\Generator as Faker;

$factory->define(Campus::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word(),
    ];
});
