<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Campus::class, function (Faker $faker) {
    return [
        'id' => 1,
        'name' => 'DEFAULT CAMPUS'
    ];
});
