<?php

namespace App\Exports;

use App\Traits\ExportExcelTrait;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SwabAntigenExport implements
    FromCollection,
    WithEvents,
    WithMapping,
    WithHeadings,
    WithColumnFormatting,
    ShouldAutoSize,
    WithTitle
{
    use ExportExcelTrait;

    public function __construct($models, $number)
    {
        $this->setUp($models, $number);
    }

    public function map($model): array
    {
        return [
            $this->number++,
            $model->nama_pasien,
            $model->tanggal_lahir,
            $model->jenis_kelamin,
            $model->no_telp,
            $model->warganegara,
            $model->negara_asal,
            $model->jenis_identitas,
            $model->no_identitas . ' ',
            $model->kategori,
            $model->alamat,
            $model->provinsi->nama,
            $model->kota->nama,
            $model->kecamatan->nama,
            $model->kelurahan->nama,
            $model->rt,
            $model->rw,
            $model->kondisi_pasien,
            $model->tanggal_gejala,
            $model->jenis_gejala,
            $model->tujuan_pemeriksaan,
            $model->tujuan_pemeriksaan_lainnya,
            $model->jenis_antigen,
            $model->no_hasil,
            $model->tanggal_periksa,
            $model->hasil_periksa,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function setTitle()
    {
        $this->title = "Swab Antigen";
    }

    public function setCellRange()
    {
        $this->cellRange = "Z";
    }

    public function setHeader()
    {
        $this->header = [
            'No',
            'Nama Pasien',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'No Telp',
            'Warganegara',
            'Negara Asal',
            'Jenis Identitas',
            'No Identitas',
            'Kategori',
            'Alamat',
            'Provinsi',
            'Kabupaten',
            'Kecamatan',
            'Kelurahan',
            'RT',
            'RW',
            'Kondisi Pasien',
            'Tanggal Gejala',
            'Jenis Gejala',
            'Tujuan Pemeriksaan',
            'Tujuan Pemeriksaan Lainnya',
            'Jenis Antigen',
            'No Hasil',
            'Tanggal Periksa',
            'Hasil Periksa',
        ];
    }
}
