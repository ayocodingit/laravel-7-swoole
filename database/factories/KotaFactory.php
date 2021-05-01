<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Kota;
use App\Models\Provinsi;
use Faker\Generator as Faker;

$factory->define(Kota::class, function (Faker $faker) {
    return [
        'id' => 1,
        'nama' => $faker->city(),
        'provinsi_id' => factory(Provinsi::class)->create()->id
    ];
});
