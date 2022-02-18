<?php

/* @var $factory Factory */

use App\Models\GradeTypeCategory;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\GradeType::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
        'total' => $faker->randomNumber(),
        'grade_type_category_id' => factory(GradeTypeCategory::class)->create()->id,
        'deleted_at' => $faker->dateTime(),
    ];
});
