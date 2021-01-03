<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\GradeTypeCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
