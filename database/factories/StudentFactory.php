<?php

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(App\Models\Student::class, function (Faker $faker) {
    return [
        'idnumber' => $faker->randomNumber($nbDigits = 8),
        'address' => $faker->streetAddress,
        'birthdate' => $faker->dateTimeThisCentury->format('Y-m-d'),
        'id' => factory(User::class)->create()->id,
        'gender_id' => rand(0, 2),
    ];
});
