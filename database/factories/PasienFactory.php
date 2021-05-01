<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Pasien;
use Faker\Generator as Faker;
use App\Enums\JenisKelaminEnum;
use App\Enums\KewarganegaraanEnum;
use App\Models\Kota;

$factory->define(Pasien::class, function (Faker $faker) {
    return [
        'nama_lengkap' => $faker->name,
        'nik' => $faker->randomNumber(),
        'tanggal_lahir' => now(),
        'tempat_lahir' => $faker->city,
        'kewarganegaraan' => $faker->randomElement(KewarganegaraanEnum::getValues()),
        'no_telp' => '0880123123123',
        'pekerjaan' => 'DIREKTUR',
        'jenis_kelamin' => $faker->randomElement(JenisKelaminEnum::getValues()),
        'kota_id' => 1
    ];
});
