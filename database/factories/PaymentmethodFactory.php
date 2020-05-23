<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Paymentmethod::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'code' => $faker->word,
    ];
});
