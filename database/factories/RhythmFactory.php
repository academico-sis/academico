<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Rhythm::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word(),
    ];
});
