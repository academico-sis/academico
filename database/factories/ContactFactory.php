<?php

use App\Models\Contact;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Contact::class, function (Faker $faker) {
    return [
        'student_id' => 1,
        'firstname' => $faker->name,
        'lastname' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'idnumber' => '121212SS',
        'address' => 'Example Street 54',
    ];
});
