<?php

use App\Models\Student;
use App\Models\User;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Student::class, function (Faker $faker) {
    return [
        'idnumber' => $faker->randomNumber($nbDigits = 8),
        'genre_id' => '1',
        'address' => $faker->streetAddress,
        'birthdate' => $faker->dateTimeThisCentury->format('Y-m-d'),
        'id' => factory(User::class)->create()->id,
    ];
});
