<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Skills\SkillEvaluation::class, function (Faker $faker) {
    return [
        'enrollment_id' => factory(App\Models\Enrollment::class),
        'skill_scale_id' => factory(App\Models\Skills\SkillScale::class),
        'skill_id' => factory(App\Models\Skills\Skill::class),
        'deleted_at' => $faker->dateTime(),
    ];
});
