<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Period::class, function (Faker $faker) {
    return [
        'name' => 'CURRENT PERIOD',
        'start' => date('Y-m-d', strtotime("-90 days")),
        'end' => date('Y-m-d', strtotime("+90 days")),
        'year_id' => 1
    ];
});
