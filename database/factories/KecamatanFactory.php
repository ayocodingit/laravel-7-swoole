<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Kecamatan;
use App\Models\Kota;
use Faker\Generator as Faker;

$factory->define(Kecamatan::class, function (Faker $faker) {
    return [
        'id' => 1,
        'nama' => $faker->city,
        'kota_id' => factory(Kota::class)->create()->id
    ];
});
