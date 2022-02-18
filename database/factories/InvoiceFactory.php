<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

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
