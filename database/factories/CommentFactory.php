<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Models\Comment::class, function (Faker $faker) {
    return [
        'commentable_id' => $faker->randomNumber(),
        'commentable_type' => $faker->word(),
        'body' => $faker->text(),
        'action' => $faker->boolean(),
        'author_id' => factory(App\Models\User::class),
    ];
});
