<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\GradeType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'total' => $faker->randomNumber(),
        'deleted_at' => $faker->dateTime(),
    ];
});
