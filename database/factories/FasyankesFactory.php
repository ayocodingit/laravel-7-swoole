<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\FasyankesPengirimEnum;
use App\Models\Fasyankes;
use Faker\Generator as Faker;

$factory->define(Fasyankes::class, function (Faker $faker) {
    return [
        'tipe' => $faker->randomElement(FasyankesPengirimEnum::getAll()),
        'nama' => $faker->name()
    ];
});
