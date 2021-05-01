<?php

namespace App\Imports;

use App\Enums\JenisRegistrasiEnum;
use App\Enums\StatusPasienEnum;
use App\Models\Fasyankes;
use App\Models\Kota;
use App\Models\Sampel;
use App\Rules\ExistsSampel;
use App\Traits\ImportExcelTrait;
use App\Traits\RegisterTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Enum\Laravel\Rules\EnumValueRule;

class RegisterRujukanImport implements ToCollection, WithHeadingRow
{
    use RegisterTrait;
    use Importable;
    use ImportExcelTrait;

    const RULES = [
        'telp_fasyankes' => 'nullable|regex:@^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$@',
        'kunjungan' => 'nullable|integer',
        'id_fasyankes' => [
            'required',
            'integer',
            'exists:fasyankes,id',
        ],
        'kategori' => 'nullable',
        'suhu' => 'nullable',
        'tanggal_kunjungan' => 'nullable|date|date_format:Y-m-d',
        'rs_kunjungan' => 'nullable',
        'nik' => 'nullable|digits:16',
        'nama_pasien' => 'required|min:3',
        'tanggal_lahir' => 'nullable|date|date_format:Y-m-d',
        'usia_bulan' => 'nullable|integer',
        'usia_tahun' => 'nullable|integer',
        'rt' => 'nullable|integer',
        'rw' => 'nullable|integer',
        'provinsi_id' => [
            'nullable',
            'exists:provinsi,id',
        ],
        'kota_id' => [
            'required',
            'exists:kota,id',
        ],
        'kecamatan_id' => [
            'nullable',
            'exists:kecamatan,id',
        ],
        'kelurahan_id' => [
            'nullable',
            'exists:kelurahan,id',
        ],
        'suhu' => ['nullable', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
        'alamat' => 'required',
        'kewarganegaraan' => 'nullable',
        'tempat_lahir' => 'nullable',
        'no_hp' => 'nullable',
        'jenis_kelamin' => 'nullable',
        'hasil_rdt' => 'nullable'
    ];

    public function collection(Collection $rows)
    {
        $this->checkValidLimit($rows);
        foreach ($rows as $key => $row) {
            if (!$row->get('no')) {
                continue;
            }
            $row->kriteria = strtolower($row->get('kriteria'));
            $row->nomor_sampel = trim($row->get('nomor_sampel'));
            $this->validated($row->toArray(), $key);
            $this->setData($this->mappingData($row));
        }
    }

    private function mappingData($row)
    {
        return [
            'sumber_pasien' => $row->get('kategori'),
            'kunjungan_ke' => $row->get('kunjungan'),
            'tanggal_kunjungan' => $row->get('tanggal_kunjungan'),
            'rs_kunjungan' => $row->get('rs_kunjungan'),
            'fasyankes_id' => $row->get('id_fasyankes'),
            'fasyankes_nama' => Fasyankes::where('id', $row->get('id_fasyankes'))->value('nama'),
            'nama_dokter' => $row->get('dokter'),
            'no_telp' => $row->get('telp_fasyankes'),
            'nik' => $row->get('nik'),
            'nama_lengkap' => $row->get('nama_pasien'),
            'kewarganegaraan' => $row->get('kewarganegaraan'),
            'jenis_kelamin' => $row->get('jenis_kelamin'),
            'tanggal_lahir' => $row->get('tanggal_lahir'),
            'tempat_lahir' => $row->get('tempat_lahir'),
            'provinsi_id' => $row->get('provinsi_id'),
            'kota_id' => $row->get('kota_id'),
            'kota_nama' => Kota::where('id', $row->get('kota_id'))->value('nama'),
            'kecamatan_id' => $row->get('kecamatan_id'),
            'kelurahan_id' => $row->get('kelurahan_id'),
            'no_hp' => $row->get('no_hp'),
            'no_rw' => $row->get('rw'),
            'no_rt' => $row->get('rt'),
            'alamat_lengkap' => $row->get('alamat'),
            'keterangan_lain' => $row->get('keterangan'),
            'suhu' => $row->get('suhu'),
            'usia_tahun' => $row->get('usia_tahun'),
            'usia_bulan' => $row->get('usia_bulan'),
            'status' => $row->get('kriteria'),
            'nomor_sampel' => $row->get('nomor_sampel'),
            'hasil_rdt' => $row->get('hasil_rdt'),
            'jenis_registrasi' => JenisRegistrasiEnum::rujukan(),
        ];
    }

    public function rules(): array
    {
        return array_merge(self::RULES, [
            'kriteria' => new EnumValueRule(StatusPasienEnum::class),
            'nomor_sampel' => [
                'required',
                'regex:/^' . Sampel::NUMBER_FORMAT_RUJUKAN . '$/',
                new ExistsSampel(),
            ],
        ]);
    }
}
