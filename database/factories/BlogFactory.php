<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Blog::class,function (Faker $faker) {
    return [
        'user_id' => function(){ return (factory(\App\User::class)->create())->id; },
        'title' => $faker->sentence(),
        'description' => $faker->sentence(100),
    ];});
