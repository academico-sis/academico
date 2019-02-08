<?php

use Carbon\Carbon;
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


$factory->define(App\Models\Student::class, function (Faker $faker) {
    return [
        'idnumber' => '123AK456',
        'genre_id' => '1',
        'address' => 'Example Lane 123',
        'birthdate' => Carbon::parse("20 years ago"),
    ];
});