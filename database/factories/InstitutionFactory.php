<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Institution::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
