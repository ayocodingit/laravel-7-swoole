<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PelacakanSampelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'nomor_sampel'          => $this->nomor_sampel,
            'sampel_status'         => $this->sampel_status,
            'nama_lengkap'          => $this->nama_lengkap,
            'usia_tahun'            => $this->usia_tahun,
            'nik'                   => $this->nik,
            'jenis_kelamin'         => $this->jenis_kelamin,
            'sumber_pasien'         => $this->sumber_pasien,
            'nama_rs'               => $this->nama_rs,
            'nama_kota'             => $this->nama_kota,
            'kesimpulan_pemeriksaan' => $this->kesimpulan_pemeriksaan,
            'tanggal_lahir'         => $this->tanggal_lahir,
            'nomor_register'        => $this->nomor_register,
            'nama_validator'        => optional($this->validator)->nama,
            'waktu_sample_taken'    => $this->waktu_sample_taken,
            'kondisi_sampel'        => $this->petugas_pengambilan_sampel,
            'kunjungan_ke'          => $this->kunjungan_ke,
            'deskripsi'             => $this->deskripsi,
            'fasyankes_nama'        => $this->fasyankes_nama
        ];
    }
}
