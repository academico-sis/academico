<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Student;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/


$factory->define(Student::class, function (Faker $faker) {
    return [
        'idnumber' => $faker->randomNumber($nbDigits = 8),
        'genre_id' => '1',
        'address' => $faker->streetAddress,
        'birthdate' => $faker->dateTimeThisCentury->format('Y-m-d'),
        'user_id' => factory(User::class)->create()->id
    ];
});