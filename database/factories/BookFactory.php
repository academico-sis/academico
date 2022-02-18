<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Book::class, function (Faker $faker) {
    return [
        'name' => $faker->word(),
        'price' => $faker->randomFloat(),
        'product_code' => $faker->word(),
    ];
});
