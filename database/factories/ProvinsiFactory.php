<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Provinsi;
use Faker\Generator as Faker;

$factory->define(Provinsi::class, function (Faker $faker) {
    return [
        'id' => 1,
        'nama' => $faker->city(),
    ];
});
