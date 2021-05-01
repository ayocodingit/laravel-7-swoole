<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Role;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'roles_name' => $faker->randomElement([
            'Admin',
            'Admin Registrasi',
            'Admin Sample',
            'Admin Ekstraksi',
            'Admin PCR',
            'Verifikator',
            'Validator',
            'Admin Swab Antigen'
        ]),
    ];
});
