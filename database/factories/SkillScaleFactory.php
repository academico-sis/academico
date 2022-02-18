<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Skills\SkillScale::class, function (Faker $faker) {
    return [
        'shortname' => $faker->text(),
        'name' => $faker->name(),
        'value' => $faker->randomFloat(),
        'deleted_at' => $faker->dateTime(),
    ];
});
