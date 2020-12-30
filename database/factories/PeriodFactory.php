<?php

use App\Models\Period;
use App\Models\Year;
use Carbon\Carbon;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Period::class, function (Faker $faker) {
    $start = $faker->date;

    return [
        'name' => $faker->unique()->randomNumber,
        'start' => Carbon::parse($start),
        'end' => Carbon::parse($start)->addDays(90),
        'year_id' => factory(Year::class)->create()->id,
    ];
});
