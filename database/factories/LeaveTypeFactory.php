<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\LeaveType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
