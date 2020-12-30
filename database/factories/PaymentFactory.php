<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Payment::class, function (Faker $faker) {
    return [
        'responsable_id' => $faker->randomNumber(),
        'enrollment_id' => factory(App\Models\Enrollment::class),
        'payment_method' => $faker->word,
        'value' => $faker->randomFloat(),
        'comment' => $faker->word,
    ];
});
