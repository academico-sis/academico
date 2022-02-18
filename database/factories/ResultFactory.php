<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Result::class, function (Faker $faker) {
    return [
        'enrollment_id' => factory(App\Models\Enrollment::class),
        'result_type_id' => factory(App\Models\ResultType::class),
    ];
});
