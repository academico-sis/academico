<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\GradeType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'total' => $faker->randomNumber(),
        'deleted_at' => $faker->dateTime(),
    ];
});
