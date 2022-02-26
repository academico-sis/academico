<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Skills\Skill::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
        'default_weight' => 1,
        'level_id' => factory(App\Models\Level::class),
        'skill_type_id' => factory(App\Models\Skills\SkillType::class),
        'deleted_at' => $faker->dateTime(),
        'order' => $faker->randomNumber(),
    ];
});
