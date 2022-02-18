<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\PhoneNumber::class, function (Faker $faker) {
    return [
        'phoneable_id' => $faker->randomNumber(),
        'phoneable_type' => $faker->word(),
        'phone_number' => $faker->phoneNumber(),
    ];
});
