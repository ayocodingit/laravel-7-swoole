<?php

namespace App\Traits;

use App\Models\Register;
use App\Models\Pasien;
use App\Models\PasienRegister;
use App\Models\Sampel;

trait TestTrait
{
    public function addRegisterData()
    {
        $register = factory(Register::class)->create(['creator_user_id' => $this->user->id]);
        $pasien = factory(Pasien::class)->create();
        factory(PasienRegister::class)->create(['register_id' => $register->id, 'pasien_id' => $pasien->id]);
        factory(Sampel::class)->create([
            'nomor_register' => $register->nomor_register,
            'register_id' => $register->id,
        ]);

        return [
            'register' => $register,
            'pasien' => $pasien
        ];
    }
}
