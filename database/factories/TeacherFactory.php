<?php

use App\Models\Teacher;
use App\Models\User;
use Faker\Generator as Faker;


$factory->define(Teacher::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id,
        'max_week_hours' => 25,
    ];
});
