<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ResultType::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
        'description' => $faker->text(),
    ];
});
