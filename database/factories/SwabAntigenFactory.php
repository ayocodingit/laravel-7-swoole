<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\HasilPeriksaEnum;
use App\Enums\JenisIdentitasEnum;
use App\Enums\JenisKelaminEnum;
use App\Enums\KewarganegaraanEnum;
use App\Enums\KondisiPasienEnum;
use App\Enums\StatusAntigenEnum;
use App\Enums\TujuanPemeriksaanEnum;
use Carbon\Carbon;
use Faker\Generator as Faker;
use App\Models\SwabAntigen;

$factory->define(SwabAntigen::class, function (Faker $faker) {
    return [
        'nama_pasien' => $faker->name(),
        'status' => $faker->randomElement(StatusAntigenEnum::getAll()),
        'nomor_registrasi' => $faker->unique()->randomNumber(),
        'tanggal_lahir' => Carbon::now(),
        'usia_tahun' => rand(10,30),
        'usia_bulan' => rand(10,30),
        'jenis_kelamin' => $faker->randomElement(JenisKelaminEnum::getAll()),
        'no_telp' => rand(),
        'warganegara' => $faker->randomElement(KewarganegaraanEnum::getAll()),
        'negara_asal' => $faker->country(),
        'jenis_identitas' => $faker->randomElement(JenisIdentitasEnum::getAll()),
        'no_identitas' => rand(),
        'kategori' => $faker->name(),
        'alamat' => $faker->address(),
        'kode_provinsi' => 1,
        'kode_kota_kabupaten' => 1,
        'kode_kecamatan' => 1,
        'kode_kelurahan' => 1,
        'rt' => rand(1,999),
        'rw' => rand(1,999),
        'kondisi_pasien' => $faker->randomElement(KondisiPasienEnum::getAll()),
        'tanggal_gejala' => Carbon::now(),
        'no_hasil' => $faker->unique()->randomNumber(),
        'hasil_periksa' => $faker->randomElement(HasilPeriksaEnum::getAll()),
        'tujuan_pemeriksaan' => $faker->randomElement(TujuanPemeriksaanEnum::getIndices())
    ];
});
