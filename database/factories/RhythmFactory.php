<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Rhythm::class, function (Faker $faker) {
    return [
        'id' => 1,
        'name' => 'DEFAULT RHYTHM'
    ];
});
