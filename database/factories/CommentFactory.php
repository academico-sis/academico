<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Comment::class, function (Faker $faker) {
    return [
        'commentable_id' => $faker->randomNumber(),
        'commentable_type' => $faker->word,
        'body' => $faker->text,
        'action' => $faker->boolean,
        'author_id' => factory(App\Models\User::class),
    ];
});
