<?php

namespace App\Traits;

use App\Models\Register;
use Illuminate\Support\Facades\DB;

/**
 * Register trait
 *
 */
trait RegisterTrait
{
    use ConvertEnumTrait;

    /**
     * Generate nomor register
     * @param string $date
     * @param string $jenisRegistrasi
     *
     * @return string
     */
    public function generateNomorRegister($date = null, $jenisRegistrasi = null)
    {
        if (!$date) {
            $date = date('Ymd');
        }
        $kodeRegistrasi = 'L';
        if ($jenisRegistrasi === 'mandiri') {
            $kodeRegistrasi = 'L';
        } elseif ($jenisRegistrasi === 'rujukan') {
            $kodeRegistrasi = 'R';
        }
        $res = DB::select(
            "select max(right(nomor_register, 4))::int8 val
            from register where nomor_register ilike '{$kodeRegistrasi}{$date}%'"
        );
        $nextnum = 1;
        if (count($res)) {
            $nextnum = $res[0]->val + 1;
        }
        return $this->getUniqueNomorRegister($kodeRegistrasi, $date, $nextnum);
    }

    protected function getUniqueNomorRegister($kodeRegistrasi, $date, $nextnum)
    {
        do {
            $nomorRegister = $kodeRegistrasi . $date . str_pad($nextnum, 4, "0", STR_PAD_LEFT);
            $nextnum++;
        } while (Register::where('nomor_register', 'ilike', $nomorRegister . '%')->withTrashed()->exists());
        return $nomorRegister;
    }

    protected function getRequestRegister($request)
    {
        $sumber_pasien = $request->input('reg_sumberpasien');
        if ($request->input('reg_sumberpasien') != "Umum") {
            $sumber_pasien = $request->input('reg_sumberpasien_isian');
        }
        return [
            'tanggal_kunjungan' => $request->input('reg_tanggalkunjungan'),
            'kunjungan_ke' => $request->input('reg_kunjungan_ke'),
            'rs_kunjungan' => $request->input('reg_rsfasyankes'),
            'sumber_pasien' => $sumber_pasien
        ];
    }

    protected function getRequestPasien($request)
    {
        return [
            'nama_lengkap' => $request->input('reg_nama_pasien'),
            'kewarganegaraan' => $request->input('reg_kewarganegaraan'),
            'nik' => $request->input('reg_nik'),
            'tempat_lahir' => $request->input('reg_tempatlahir'),
            'tanggal_lahir' => $request->input('reg_tgllahir'),
            'no_hp' => $request->input('reg_nohp'),
            'kota_id' => $request->input('reg_kota_id'),
            'kecamatan_id' => $request->input('reg_kecamatan_id'),
            'kelurahan_id' => $request->input('reg_kelurahan_id'),
            'kecamatan' => $request->input('reg_kecamatan'),
            'kelurahan' => $request->input('reg_kelurahan'),
            'provinsi_id' => $request->input('reg_provinsi_id'),
            'alamat_lengkap' => $request->input('reg_alamat'),
            'no_rt' => $request->input('reg_rt'),
            'no_rw' => $request->input('reg_rw'),
            'suhu' => $request->input('reg_suhu'),
            'jenis_kelamin' => $request->input('reg_jk'),
            'keterangan_lain' => $request->input('reg_keterangan'),
            'usia_tahun' => $request->input('reg_usia_tahun'),
            'usia_bulan' => $request->input('reg_usia_bulan'),
            'status' => $request->input('status'),
        ];
    }

    protected function registerLogs($registerOrigin, $registerChanges)
    {
        $registerLogs = array();
        foreach ($registerChanges as $key => $value) {
            if ($key == "updated_at") {
                continue;
            }
            $registerLogs[$key]["from"] = $registerOrigin[$key];
            $registerLogs[$key]["to"] = $value;
        }
        return $registerLogs;
    }

    protected function sampelLogs($sampelOrigin, $sampelChanges)
    {
        $sampelLogs = array();
        foreach ($sampelChanges as $key => $value) {
            if ($key == "updated_at") {
                continue;
            }
            $sampelLogs[$key]["from"] = $sampelOrigin[$key];
            $sampelLogs[$key]["to"] = $value;
        }
        return $sampelLogs;
    }

    protected function pasienLogs($pasienOrigin, $pasienChanges)
    {
        $pasienLogs = array();
        foreach ($pasienChanges as $key => $value) {
            if ($key == "updated_at") {
                continue;
            }
            if ($key == 'status') {
                $pasienLogs[$key]["from"] = optional($this->convertEnumStatusPasien($pasienOrigin[$key]))->getValue();
                $pasienLogs[$key]["to"] = optional($this->convertEnumStatusPasien($value))->getValue();
            } elseif ($key == 'tanggal_lahir') {
                $pasienLogs[$key]["from"] = date('d-m-Y', strtotime($pasienOrigin[$key]));
                $pasienLogs[$key]["to"] = date('d-m-Y', strtotime($value));
            } else {
                $pasienLogs[$key]["from"] = $pasienOrigin[$key];
                $pasienLogs[$key]["to"] = $value;
            }
        }
        return $pasienLogs;
    }
}
