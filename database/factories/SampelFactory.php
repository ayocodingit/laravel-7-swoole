<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Sampel;
use Faker\Generator as Faker;

$factory->define(Sampel::class, function (Faker $faker) {
    return [
        'nomor_sampel' => $faker->randomNumber(),
        'nomor_register' => $faker->randomElement(['R','L']).rand(),
        'sampel_status' => 'waiting_sample',
        'jenis_vtm' => $faker->name(),
        'petugas_pengambilan_sampel' => $faker->name()
    ];
});
