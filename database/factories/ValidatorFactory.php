<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Validator;
use Faker\Generator as Faker;

$factory->define(Validator::class, function (Faker $faker) {
    return [
        'id' => $faker->randomNumber(),
        'nama' => $faker->name(),
        'nip' => $faker->randomNumber(),
    ];
});
