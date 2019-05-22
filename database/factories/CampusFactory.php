<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Campus::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word
    ];
});
