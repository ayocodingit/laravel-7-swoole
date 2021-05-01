<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Faker\Generator as Faker;

$factory->define(Kelurahan::class, function (Faker $faker) {
    return [
        'id' => 1,
        'nama' => $faker->city,
        'kecamatan_id' => factory(Kecamatan::class)->create()->id
    ];
});
