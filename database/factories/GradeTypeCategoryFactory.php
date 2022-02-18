<?php

/* @var Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\GradeTypeCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
    ];
});
