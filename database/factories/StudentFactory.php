<?php

use App\Models\Student;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Student::class, function (Faker $faker) {
    return [
        'idnumber' => $faker->randomNumber($nbDigits = 8),
        'genre_id' => '1',
        'address' => $faker->streetAddress,
        'birthdate' => $faker->dateTimeThisCentury->format('Y-m-d'),
        'user_id' => factory(User::class)->create()->id,
    ];
});
