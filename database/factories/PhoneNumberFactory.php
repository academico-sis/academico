<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\PhoneNumber::class, function (Faker $faker) {
    return [
        'phoneable_id' => $faker->randomNumber(),
        'phoneable_type' => $faker->word(),
        'phone_number' => $faker->phoneNumber(),
    ];
});
