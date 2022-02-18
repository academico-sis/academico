<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Skills\SkillType::class, function (Faker $faker) {
    return [
        'shortname' => $faker->word(),
        'name' => $faker->name(),
        'deleted_at' => $faker->dateTime(),
    ];
});
