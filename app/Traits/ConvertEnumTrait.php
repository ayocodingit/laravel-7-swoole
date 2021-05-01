<?php

namespace App\Traits;

use App\Enums\FasyankesPengirimEnum;
use App\Enums\KesimpulanPemeriksaanEnum;
use App\Enums\StatusPasienEnum;
use App\Enums\TujuanPemeriksaanEnum;

trait ConvertEnumTrait
{
    private function convertEnumStatusPasien($enum)
    {
        if (!$enum) {
            return $enum;
        }

        return StatusPasienEnum::make($enum);
    }

    private function convertEnumFasyankesPengirim($enum)
    {
        if (!$enum) {
            return $enum;
        }

        return FasyankesPengirimEnum::make(strtolower($enum));
    }

    private function convertEnumKesimpulanPemeriksaan($enum)
    {
        if (!$enum) {
            return $enum;
        }

        return KesimpulanPemeriksaanEnum::make(strtolower($enum));
    }

    private function convertTujuanPemeriksaanEnum($enum)
    {
        if (!$enum) {
            return $enum;
        }

        return TujuanPemeriksaanEnum::make($enum);
    }
}
