<?php

use App\Models\Level;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Level::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
    ];
});
