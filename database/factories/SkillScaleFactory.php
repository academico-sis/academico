<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Skills\SkillScale::class, function (Faker $faker) {
    return [
        'shortname' => $faker->text(),
        'name' => $faker->name(),
        'value' => $faker->randomFloat(),
        'deleted_at' => $faker->dateTime(),
    ];
});
