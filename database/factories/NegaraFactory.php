<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Negara;
use Faker\Generator as Faker;

$factory->define(Negara::class, function (Faker $faker) {
    return [
        'nama' => $faker->country(),
        'inisial' => $faker->countryCode()
    ];
});
