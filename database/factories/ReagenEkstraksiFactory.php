<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\MetodeEkstraksiEnum;
use App\Models\ReagenEkstraksi;
use Faker\Generator as Faker;

$factory->define(ReagenEkstraksi::class, function (Faker $faker) {
    return [
        'nama' => $faker->name(),
        'metode_ekstraksi' => $faker->randomElement(MetodeEkstraksiEnum::getAll())
    ];
});
