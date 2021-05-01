<?php

namespace App\Imports;

use App\Enums\JenisRegistrasiEnum;
use App\Models\Fasyankes;
use App\Models\Kota;
use App\Models\Sampel;
use App\Models\TesMasif;
use App\Rules\TesMasifRujukanExistsRule;
use App\Rules\UniqueSampel;
use App\Traits\ImportExcelTrait;
use App\Traits\RegisterTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RegisterRujukanTesMasifImport implements ToCollection, WithHeadingRow
{
    use RegisterTrait;
    use Importable;
    use ImportExcelTrait;

    public function collection(Collection $rows)
    {
        $this->checkValidLimit($rows);
        foreach ($rows as $key => $row) {
            if (!$row->get('no')) {
                continue;
            }
            $tesMasif = TesMasif::where('nomor_sampel', $row->get('nomor_sampel_fasyankes'))
                                ->where('available', true)
                                ->where('jenis_registrasi', JenisRegistrasiEnum::rujukan())
                                ->first();
            $row->nomor_sampel_labkes = trim($row->get('nomor_sampel_labkes'));
            $this->validated($row->toArray(), $key);
            $this->setData($this->mappingData($tesMasif, $row));
        }
    }

    private function mappingData($row, $rowDataExcel)
    {
        return [
            'sumber_pasien' => optional($row)->kategori,
            'kunjungan_ke' => optional($row)->kunjungan,
            'tanggal_kunjungan' => optional($row)->tanggal_kunjungan,
            'rs_kunjungan' => optional($row)->rs_kunjungan,
            'fasyankes_id' => optional($row)->fasyankes_id,
            'fasyankes_nama' => Fasyankes::where('id', optional($row)->fasyankes_id)->value('nama'),
            'nama_dokter' => optional($row)->dokter,
            'no_telp' => optional($row)->telp_fasyankes,
            'nik' => optional($row)->nik,
            'nama_lengkap' => optional($row)->nama_pasien,
            'kewarganegaraan' => optional($row)->kewarganeraan,
            'jenis_kelamin' => optional($row)->jenis_kelamin,
            'tanggal_lahir' => optional($row)->tanggal_lahir,
            'tempat_lahir' => optional($row)->tempat_lahir,
            'provinsi_id' => optional($row)->provinsi_id,
            'kota_id' => optional($row)->kota_id,
            'kota_nama' => Kota::where('id', optional($row)->kota_id)->value('nama'),
            'kecamatan_id' => optional($row)->kecamatan_id,
            'kelurahan_id' => optional($row)->kelurahan_id,
            'no_hp' => optional($row)->no_hp,
            'no_rw' => optional($row)->rw,
            'no_rt' => optional($row)->rt,
            'alamat_lengkap' => optional($row)->alamat,
            'keterangan_lain' => optional($row)->keterangan,
            'suhu' => optional($row)->suhu,
            'usia_tahun' => optional($row)->usia_tahun,
            'usia_bulan' => optional($row)->usia_bulan,
            'status' => optional($row)->kriteria,
            'nomor_sampel' => $rowDataExcel->get('nomor_sampel_labkes'),
            'nomor_sampel_fasyankes' => $rowDataExcel->get('nomor_sampel_fasyankes'),
            'hasil_rdt' => optional($row)->hasil_rdt,
            'jenis_registrasi' => JenisRegistrasiEnum::rujukan(),
            'registration_code' => optional($row)->registration_code,
            'id' => optional($row)->id
        ];
    }

    public function rules(): array
    {
        return [
            'nomor_sampel_fasyankes' => ['required', new TesMasifRujukanExistsRule()],
            'nomor_sampel_labkes' => [
                'required',
                'regex:/^' . Sampel::NUMBER_FORMAT_RUJUKAN . '$/',
                new UniqueSampel()
            ],
        ];
    }
}
