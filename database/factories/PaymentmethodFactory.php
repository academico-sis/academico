<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Paymentmethod::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'code' => $faker->word,
    ];
});
