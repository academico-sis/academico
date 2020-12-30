<?php

use App\Models\Room;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Room::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
    ];
});
