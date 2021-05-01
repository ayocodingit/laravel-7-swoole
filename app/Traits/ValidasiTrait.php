<?php

namespace App\Traits;

use App\Enums\KesimpulanPemeriksaanEnum;
use App\Events\SampleValidatedEvent;
use Illuminate\Http\Response;

trait ValidasiTrait
{
    private function getValidStatusSampelValidasi($status_sampel): string
    {
        if (!in_array($status_sampel, ['sample_valid', 'sample_verified'])) {
            $status_sampel = 'sample_valid';
        }
        return $status_sampel;
    }

    private function getNameFileValidasi($status_sampel): string
    {
        if ($status_sampel == 'sample_verified') {
            $namaFile = 'Belum divalidasi';
        } else {
            $namaFile = 'Tervalidasi';
        }
        return $namaFile . '-' . time() . '.xlsx';
    }

    private function checkValidNomorRegister($sampel)
    {
        abort_if(!$sampel->nomor_register, Response::HTTP_BAD_REQUEST, 'Nomor registrasi belum terdaftar');
    }

    private function sendSampleValidPositif($sampel)
    {
        if ($sampel->pemeriksaanSampel->kesimpulan_pemeriksaan == KesimpulanPemeriksaanEnum::positif()) {
            event(new SampleValidatedEvent($sampel));
        }
    }
}
