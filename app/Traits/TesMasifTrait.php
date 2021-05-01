<?php

namespace App\Traits;

use App\Enums\JenisRegistrasiEnum;
use App\Enums\KewarganegaraanEnum;
use App\Enums\StatusPasienEnum;
use App\Models\Sampel;
use App\Rules\ExistsWilayah;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Spatie\Enum\Laravel\Rules\EnumValueRule;

/**
 * Tes Masif trait
 *
 */
trait TesMasifTrait
{

    public $berhasil = [];
    public $gagal = [];

    private function ruleNomorSampel($row): array
    {
        $ruleNomorSampel = [
            'required',
            'unique:tes_masif,nomor_sampel',
            'unique:sampel,nomor_sampel,NULL,id,deleted_at,NULL'
        ];
        if ($row['jenis_registrasi'] == JenisRegistrasiEnum::mandiri()) {
            $ruleNomorSampel[] = 'regex:/^' . Sampel::NUMBER_FORMAT_TES_MASIF . '$/';
        }
        return $ruleNomorSampel;
    }

    public function rules($row): array
    {
        return [
            'registration_code' => 'required',
            'nama_pasien' => 'required|min:3',
            'jenis_registrasi' => ['required', new EnumValueRule(JenisRegistrasiEnum::class)],
            'nomor_sampel' => $this->ruleNomorSampel($row),
            'fasyankes_id' => $this->ruleFasyankes($row),
            'kewarganegaraan' => ['nullable', new EnumValueRule(KewarganegaraanEnum::class)],
            'kategori' => 'nullable',
            'kriteria' => ['required', new EnumValueRule(StatusPasienEnum::class)],
            'nik' => 'nullable|digits:16',
            'tanggal_lahir' => 'nullable|date',
            'provinsi_id' => ['nullable', new ExistsWilayah()],
            'kota_id' => ['nullable', new ExistsWilayah()],
            'kecamatan_id' => 'nullable',
            'kelurahan_id' => 'nullable',
            'alamat' => 'required',
            'suhu' => ['nullable', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'usia_tahun' => 'nullable|integer',
            'usia_bulan' => 'nullable|integer',
            'telp_fasyankes' => 'nullable|regex:@^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$@',
            'kunjungan' => 'nullable|integer',
            'tanggal_kunjungan' => 'required|date',
        ];
    }

    private function ruleFasyankes($row): string
    {
        $jenisRegistrasi = 'nullable';
        if ($row['jenis_registrasi'] == JenisRegistrasiEnum::rujukan()) {
            $jenisRegistrasi = 'required';
        }
        return $jenisRegistrasi . '|integer|exists:fasyankes,id';
    }

    private function isValidTesMasif($row)
    {
        App::setLocale('id');
        $validator = Validator::make($row, $this->rules($row));
        if ($validator->fails()) {
            $this->setResultGagal(
                $row['nomor_sampel'],
                'errors',
                $validator->errors()->get('*')
            );
        }
        return !$validator->fails();
    }

    private function setResultGagal($nomor_sampel, $message, $result)
    {
        array_push($this->gagal, [
            'nomor_sampel' => $nomor_sampel,
            'message' => $message,
            'result' => $result,
        ]);
    }

    private function getResult()
    {
        return [
            'berhasil' => $this->berhasil,
            'gagal' => $this->gagal,
        ];
    }
}
