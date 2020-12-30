<?php

use App\Models\Rhythm;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Rhythm::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
    ];
});
