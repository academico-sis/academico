<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Config::class, function (Faker $faker) {
    return [
        'name' => $faker->word(),
        'value' => $faker->word(),
    ];
});
