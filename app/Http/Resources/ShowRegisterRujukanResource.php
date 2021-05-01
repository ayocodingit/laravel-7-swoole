<?php

namespace App\Http\Resources;

use App\Enums\SumberPasienEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowRegisterRujukanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $register = parent::toArray($request);
        $fasyankes = $this->fasyankes;
        $pasien = $this->pasiens()->first();
        $sampel = $this->sampel;
        return [
            'result' => [
                'reg_no' => $register['nomor_register'],
                'reg_kewarganegaraan' => $pasien->kewarganegaraan,
                'reg_sumberpasien' => $register['sumber_pasien'],
                'reg_sumberpasien_isian' => $register['sumber_pasien'] != SumberPasienEnum::Umum()->getValue() ? $register['sumber_pasien'] : null,
                'reg_nama_pasien' => $pasien->nama_lengkap,
                'reg_nik' => $pasien->nik,
                'reg_tempatlahir' => $pasien->tempat_lahir,
                'reg_tgllahir' => $pasien->tanggal_lahir,
                'reg_nohp' => $pasien->no_hp,
                'reg_provinsi_id' => $pasien->provinsi_id,
                'reg_provinsi' => optional($pasien->provinsi)->nama,
                'reg_kota_id' => $pasien->kota_id,
                'reg_kota' => optional($pasien->kota)->nama,
                'reg_kecamatan_id' => $pasien->kecamatan_id,
                'reg_kecamatan' => $pasien->kecamatan,
                'reg_kelurahan_id' => $pasien->kelurahan_id,
                'reg_kelurahan' => $pasien->kelurahan,
                'reg_alamat' => $pasien->alamat_lengkap,
                'reg_rt' => $pasien->no_rt,
                'reg_rw' => $pasien->no_rw,
                'reg_suhu' => $pasien->suhu,
                'samples' => [
                    [
                        'sampel_status' => $sampel->sampel_status,
                        'nomor_sampel' => $sampel->nomor_sampel,
                        'id' => $sampel->id,
                    ]
                ],
                'reg_keterangan' => $pasien->keterangan_lain,
                'reg_jk' => $pasien->jenis_kelamin,
                'reg_kunke' => $register['kunjungan_ke'],
                'reg_tanggalkunjungan' => $register['tanggal_kunjungan'],
                'reg_rs_kunjungan' => $register['rs_kunjungan'],
                'reg_fasyankes_pengirim' => $fasyankes->tipe,
                'reg_telp_fas_pengirim' => $register['no_telp'],
                'reg_nama_dokter' => $register['nama_dokter'],
                'reg_nama_rs' => $register['nama_rs'],
                'reg_nama_rs_lainnya' => $register['other_nama_rs'],
                'daerahlain' => $register['other_dinas_pengirim'],
                'reg_dinkes_pengirim' => $register['dinkes_pengirim'],
                'reg_no' => $register['nomor_register'],
                'reg_rsfasyankes' => $register['rs_kunjungan'],
                'reg_usia_tahun' => $pasien->usia_tahun,
                'reg_usia_bulan' => $pasien->usia_bulan,
                'reg_hasil_rdt' => $register['hasil_rdt'],
                'reg_fasyankes_id' => $register['fasyankes_id'],
                'nama_rs' => $fasyankes->nama,
                'fasyankes_pengirim' => $fasyankes->tipe,
                'status' => $pasien->status,
                'pelaporan_id' => $pasien->pelaporan_id,
                'pelaporan_id_case' => $pasien->pelaporan_id_case,
            ]
        ];
    }
}
