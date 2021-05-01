<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\JenisRegistrasiEnum;
use App\Enums\SumberPasienEnum;
use App\Models\Fasyankes;
use Illuminate\Support\Str;
use App\Models\Register;
use Faker\Generator as Faker;

$factory->define(Register::class, function (Faker $faker) {
    return [
        'fasyankes_id' => factory(Fasyankes::class)->create()->id,
        'nomor_rekam_medis' => $faker->randomNumber(),
        'nama_dokter' => $faker->name,
        'no_telp' => $faker->randomNumber(),
        'sumber_pasien' => SumberPasienEnum::Umum()->getValue(),
        'register_uuid' => Str::uuid(),
        'jenis_registrasi' => $faker->randomElement(JenisRegistrasiEnum::getAll()),
        'nomor_register' => $faker->randomNumber(),
        'kunjungan_ke' => 1,
        'tanggal_kunjungan' => now(),
        'rs_kunjungan' => $faker->name,
        'dinkes_pengirim' => $faker->name,
        'other_dinas_pengirim' => $faker->name,
        'nama_rs' => $faker->name,
        'other_nama_rs' => $faker->name,
        'fasyankes_pengirim' => $faker->name,
        'hasil_rdt' => $faker->name,
        'is_from_migration' => false,
        'tes_masif_id' => $faker->randomNumber(),
        'registration_code' => $faker->randomNumber(),
    ];
});
