<?php

use App\Models\Rhythm;
use Faker\Generator as Faker;

$factory->define(Rhythm::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word(),
    ];
});
