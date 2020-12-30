<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Config::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'value' => $faker->word,
    ];
});
