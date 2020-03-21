<?php

use App\Models\Period;
use App\Models\Year;
use Faker\Generator as Faker;

$factory->define(Period::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->randomNumber,
        'start' => date('Y-m-d', strtotime('-1 day')), // todo randomize
        'end' => date('Y-m-d', strtotime('+90 days')),
        'year_id' => factory(Year::class)->create()->id,
    ];
});
