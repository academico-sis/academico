<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Room::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word
    ];
});
