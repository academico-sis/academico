<?php

use App\Models\Year;
use App\Models\Period;
use Faker\Generator as Faker;

$factory->define(Year::class, function (Faker $faker) {
    return [
        'id' => 1,
        'name' => 'DEFAULT YEAR',
    ];
});

$factory->define(Period::class, function (Faker $faker) {
    return [
        'name' => 'DEFAULT PERIOD',
        'start' => date('Y-m-d', strtotime("-1 day")),
        'end' => date('Y-m-d', strtotime("+90 days")),
        'year_id' => 1
    ];
});
