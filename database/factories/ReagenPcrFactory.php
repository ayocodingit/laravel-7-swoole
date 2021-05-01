<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ReagenPCR;
use Faker\Generator as Faker;

$factory->define(ReagenPCR::class, function (Faker $faker) {
    return [
        'nama' => $faker->name(),
        'ct_normal' => $faker->randomNumber()
    ];
});
