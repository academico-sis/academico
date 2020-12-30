<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Book::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'price' => $faker->randomFloat(),
        'product_code' => $faker->word,
    ];
});
