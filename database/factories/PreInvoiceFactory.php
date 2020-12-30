<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\PreInvoice::class, function (Faker $faker) {
    return [
        'client_name' => $faker->word,
        'client_idnumber' => $faker->word,
        'client_address' => $faker->word,
        'client_email' => $faker->word,
        'client_phone' => $faker->word,
        'total_price' => $faker->randomFloat(),
        'company_id' => $faker->randomNumber(),
        'invoice_number' => $faker->word,
    ];
});
