<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\JenisVTM;
use Faker\Generator as Faker;

$factory->define(JenisVTM::class, function (Faker $faker) {
    return [
        'nama' => $faker->name()
    ];
});
