<?php

use App\Models\UserData;
use Faker\Generator as Faker;

$factory->define(UserData::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'firstname' => $faker->name,
        'lastname' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'idnumber' => "121212SS",
        'address' => "Example Street 54",
    ];
});
