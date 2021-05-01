<?php

namespace App\Imports;

use App\Enums\HasilPeriksaEnum;
use App\Enums\JenisAntigenEnum;
use App\Enums\JenisGejalaEnum;
use App\Enums\JenisIdentitasEnum;
use App\Enums\JenisKelaminEnum;
use App\Enums\KewarganegaraanEnum;
use App\Enums\KondisiPasienEnum;
use App\Enums\TujuanPemeriksaanEnum;
use App\Models\SwabAntigen;
use App\Traits\SwabAntigenTrait;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use Spatie\Enum\Laravel\Rules\EnumIndexRule;
use Spatie\Enum\Laravel\Rules\EnumRule;

class SwabAntigenImport implements WithHeadingRow, WithChunkReading, WithValidation, OnEachRow
{
    use Importable;
    use SwabAntigenTrait;

    public function onRow(Row $row)
    {
        SwabAntigen::create($row->toArray() + [
            'nomor_registrasi' => $this->generateNomorRegister()
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nama_pasien' => 'required',
            '*.tanggal_lahir' => 'nullable|date|date_format:Y-m-d',
            '*.usia_tahun' => 'nullable|integer',
            '*.usia_bulan' => 'nullable|integer',
            '*.jenis_kelamin' =>  [
                'nullable',
                new EnumRule(JenisKelaminEnum::class),
            ],
            '*.no_telp' => 'required|max:16',
            '*.warganegara' => [
                'required',
                new EnumRule(KewarganegaraanEnum::class),
            ],
            '*.negara_asal' => 'required_if:warganegara,' . KewarganegaraanEnum::WNA(),
            '*.jenis_identitas' => [
                'required',
                new EnumRule(JenisIdentitasEnum::class),
            ],
            '*.no_identitas' => 'required|digits:16',
            '*.kategori' => 'nullable',
            '*.alamat' => 'required',
            '*.kode_provinsi' => 'required|integer|exists:provinsi,id',
            '*.kode_kota_kabupaten' => 'required|integer|exists:kota,id',
            '*.kode_kecamatan' => 'required|integer|exists:kecamatan,id',
            '*.kode_kelurahan' => 'required|integer|exists:kelurahan,id',
            '*.rw' => 'nullable|max:3',
            '*.rt' => 'nullable|max:3',
            '*.kondisi_pasien' => [
                'required',
                new EnumRule(KondisiPasienEnum::class),
            ],
            '*.tanggal_gejala' => 'nullable|date|date_format:Y-m-d',
            '*.jenis_gejala' => [
                'nullable',
                new EnumRule(JenisGejalaEnum::class),
            ],
            '*.tujuan_pemeriksaan' => [
                'nullable',
                new EnumIndexRule(TujuanPemeriksaanEnum::class),
            ],
            '*.tujuan_pemeriksaan_lainnya' => 'required_if:tujuan_pemeriksaan,' . TujuanPemeriksaanEnum::lainnya()->getIndex(),
            '*.jenis_antigen' => [
                'nullable',
                new EnumRule(JenisAntigenEnum::class),
            ],
            '*.no_hasil' => [
                'required',
                'unique:swab_antigen,no_hasil,NULL,id,deleted_at,NULL',
                'regex:/^' . SwabAntigen::NO_HASIL . '$/',
                'distinct'
            ],
            '*.tanggal_periksa' => 'nullable|date|date_format:Y-m-d',
            '*.hasil_periksa' =>  [
                'required',
                new EnumRule(HasilPeriksaEnum::class),
            ],
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
