<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Skills\SkillType::class, function (Faker $faker) {
    return [
        'shortname' => $faker->word(),
        'name' => $faker->name(),
        'deleted_at' => $faker->dateTime(),
    ];
});
