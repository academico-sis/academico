<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Level::class, function (Faker $faker) {
    return [
        'id' => 1,
        'name' => 'DEFAULT LEVEL'
    ];
});
