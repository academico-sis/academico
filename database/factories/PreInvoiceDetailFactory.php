<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\PreInvoiceDetail::class, function (Faker $faker) {
    return [
        'pre_invoice_id' => factory(App\Models\PreInvoice::class),
        'product_name' => $faker->word,
        'product_code' => $faker->word,
        'product_id' => $faker->randomNumber(),
        'product_type' => $faker->word,
        'price' => $faker->randomFloat(),
    ];
});
