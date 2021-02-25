<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Payment::class, function (Faker $faker) {
    return [
        'responsable_id' => $faker->randomNumber(),
        'invoice_id' => factory(App\Models\Invoice::class),
        'payment_method' => $faker->word,
        'value' => $faker->randomFloat(),
        'comment' => $faker->word,
    ];
});
