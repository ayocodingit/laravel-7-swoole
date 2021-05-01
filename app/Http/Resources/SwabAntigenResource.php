<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SwabAntigenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'nomor_registrasi'    => $this->nomor_registrasi,
            'nama_pasien'         => $this->nama_pasien,
            'no_identitas'        => $this->no_identitas,
            'usia_tahun'          => $this->usia_tahun,
            'tanggal_lahir'       => $this->tanggal_lahir,
            'kategori'            => $this->kategori,
            'tanggal_periksa'     => $this->tanggal_periksa,
            'tanggal_validasi'    => $this->tanggal_validasi,
            'status'              => $this->status,
            'no_hasil'            => $this->no_hasil,
            'hasil_periksa'       => $this->hasil_periksa,
            'waktu_sampel_print'  => $this->waktu_sampel_print,
            'counter_print_hasil' => $this->counter_print_hasil,
            'validator'           => $this->validator,
            'kota'                => $this->kota,
            'provinsi'            => $this->provinsi,
            'kecamatan'           => $this->kecamatan,
            'kelurahan'           => $this->kelurahan,
        ];
    }
}
