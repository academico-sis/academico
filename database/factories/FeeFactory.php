<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Fee::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
        'price' => $faker->randomFloat(),
        'product_code' => $faker->word(),
    ];
});
