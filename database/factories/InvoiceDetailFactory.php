<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\InvoiceDetail::class, function (Faker $faker) {
    return [
        'invoice_id' => factory(App\Models\Invoice::class),
        'product_name' => $faker->word(),
        'product_code' => $faker->word(),
        'product_id' => $faker->randomNumber(),
        'product_type' => $faker->word(),
        'price' => $faker->randomFloat(),
    ];
});
