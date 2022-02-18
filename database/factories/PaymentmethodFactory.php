<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Paymentmethod::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
        'code' => $faker->word(),
    ];
});
