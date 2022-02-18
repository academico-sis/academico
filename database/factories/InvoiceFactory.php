<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Invoice::class, function (Faker $faker) {
    return [
        'client_name' => $faker->word(),
        'client_idnumber' => $faker->word(),
        'client_address' => $faker->word(),
        'client_email' => $faker->word(),
        'client_phone' => $faker->word(),
        'company_id' => $faker->randomNumber(),
        'receipt_number' => $faker->word(),
    ];
});
