<?php

namespace App\Traits;

use App\Enums\JenisRegistrasiEnum;
use App\Enums\SumberPasienEnum;
use App\Models\Fasyankes;
use Illuminate\Support\Str;

/**
 * RegisterRujukanTrait trait
 *
 */
trait RegisterRujukanTrait
{
    use RegisterTrait;

    public function getRegisterRujukanRequest($request)
    {
        $fasyankes = Fasyankes::find($request->input('reg_fasyankes_id'));
        $sumberPasien = $request->input('reg_sumberpasien_isian');
        if ($request->input('reg_sumberpasien') == SumberPasienEnum::Umum()->getValue()) {
            $sumberPasien = $request->input('reg_sumberpasien');
        }
        return [
            'nomor_rekam_medis' => null,
            'creator_user_id' => auth()->user()->id,
            'sumber_pasien' => $sumberPasien,
            'jenis_registrasi' => JenisRegistrasiEnum::rujukan()->getValue(),
            'dinkes_pengirim' => $request->input('reg_dinkes_pengirim'),
            'other_dinas_pengirim' => $request->input('daerahlain'),
            'fasyankes_id' => $request->input('reg_fasyankes_id'),
            'fasyankes_pengirim' => $request->input('reg_fasyankes_pengirim'),
            'nama_rs' => optional($fasyankes)->nama ?? $request->input('reg_nama_rs'),
            'other_nama_rs' => $request->input('reg_nama_rs_lainnya'),
            'nama_dokter' => $request->input('reg_nama_dokter'),
            'no_telp' => $request->input('reg_telp_fas_pengirim'),
            'tanggal_kunjungan' => $request->input('reg_tanggalkunjungan'),
            'kunjungan_ke' => $request->input('reg_kunke'),
            'rs_kunjungan' => $request->input('reg_rsfasyankes'),
            'hasil_rdt' => null
        ];
    }

    public function getPasienRujukanRequest($request)
    {
        return [
            'nama_lengkap' => $request->input('reg_nama_pasien'),
            'kewarganegaraan' => $request->input('reg_kewarganegaraan'),
            'nik' => $request->input('reg_nik'),
            'tempat_lahir' => $request->input('reg_tempatlahir'),
            'tanggal_lahir' => $request->input('reg_tgllahir'),
            'no_hp' => $request->input('reg_nohp'),
            'provinsi_id' => $request->input('reg_provinsi_id'),
            'kota_id' => $request->input('reg_kota_id'),
            'kecamatan_id' => $request->input('reg_kecamatan_id'),
            'kelurahan_id' => $request->input('reg_kelurahan_id'),
            'kecamatan' => $request->input('reg_kecamatan'),
            'kelurahan' => $request->input('reg_kelurahan'),
            'alamat_lengkap' => $request->input('reg_alamat'),
            'no_rt' => $request->input('reg_rt'),
            'no_rw' => $request->input('reg_rw'),
            'suhu' => parseDecimal($request->input('reg_suhu')),
            'jenis_kelamin' => $request->input('reg_jk'),
            'keterangan_lain' => $request->input('reg_keterangan'),
            'usia_tahun' => $request->input('reg_usia_tahun'),
            'usia_bulan' => $request->input('reg_usia_bulan'),
            'status' => $request->input('status'),
            'pelaporan_id' => $request->input('pelaporan_id'),
            'pelaporan_id_case' => $request->input('pelaporan_id_case'),
        ];
    }
}
