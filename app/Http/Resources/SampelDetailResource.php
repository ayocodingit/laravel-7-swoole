<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SampelDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sampel = parent::toArray($request);
        $logs = $this->logs()->orderBy('created_at', 'desc')->limit(5)->get();
        $register = optional($this)->register;
        $pasien = optional($register)->pasiens();
        $pasien = optional($pasien)->with(['kota', 'kecamatan', 'kelurahan', 'provinsi']);
        $pasien = optional($pasien)->first();
        $fasyankes = optional($this->register)->fasyankes;
        $lastPemeriksaanSampel = $this->pemeriksaanSampel()
                                    ->orderBy('tanggal_input_hasil', 'desc')
                                    ->first();
        return [
            'data' => $sampel + [
                'pemeriksaan_sampel' => $this->pemeriksaanSampel,
                'status' => $this->status,
                'register' => $this->register,
                'validator' => $this->validator,
                'ekstraksi' => $this->ekstraksi,
                'logs' => $this->receiveCount($logs),
                'pasien' => $pasien,
                'fasyankes' => $fasyankes,
                'pengambilanSampel' => $this->pengambilanSampel,
                'last_pemeriksaan_sampel' => $lastPemeriksaanSampel,
            ],
        ];
    }

    private function receiveCount($logs)
    {
        $receive_pcr_count = 1;
        $receive_extraction_count = 1;
        foreach ($logs as $key => $log) {
            if ($log->description == 'Receive PCR') {
                $receive_pcr_count++;
                if ($receive_pcr_count >= 2) {
                    $logs[$key]['re_pcr'] = 're-pcr';
                }
            }
            if ($log->description == 'Receive Extraction') {
                $receive_extraction_count++;
                if ($receive_extraction_count >= 2) {
                    $logs[$key]['re_extraction'] = 're-extraction';
                }
            }
        }
        return $logs;
    }
}
