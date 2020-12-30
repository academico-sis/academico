<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Skills\SkillEvaluation::class, function (Faker $faker) {
    return [
        'course_id' => factory(App\Models\Course::class),
        'student_id' => factory(App\Models\Student::class),
        'skill_scale_id' => factory(App\Models\Skills\SkillScale::class),
        'skill_id' => factory(App\Models\Skills\Skill::class),
        'deleted_at' => $faker->dateTime(),
    ];
});
