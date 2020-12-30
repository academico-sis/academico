<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ResultType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->text,
    ];
});
