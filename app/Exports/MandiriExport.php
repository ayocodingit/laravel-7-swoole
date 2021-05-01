<?php

namespace App\Exports;

use App\Traits\ConvertEnumTrait;
use App\Traits\ExportExcelTrait;
use App\Traits\ExportMappingTrait;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MandiriExport implements
    FromCollection,
    WithEvents,
    WithMapping,
    WithHeadings,
    WithColumnFormatting,
    ShouldAutoSize,
    WithTitle
{
    use ExportExcelTrait;
    use ConvertEnumTrait;
    use ExportMappingTrait;

    public function __construct($models, $number)
    {
        $this->setUp($models, $number);
    }

    public function map($model): array
    {
        return [
            $this->number++,
            $model->nomor_register,
            $model->nomor_sampel,
            $model->sumber_pasien,
            optional($this->convertEnumStatusPasien($model->status))->getValue(),
            $model->nama_lengkap,
            $model->nik . ' ',
            usiaPasien($model->tanggal_lahir, $model->usia_tahun),
            'Tahun',
            $model->tempat_lahir,
            $model->tanggal_lahir,
            $model->jenis_kelamin,
            $model->provinsi,
            $model->nama_kota,
            $model->kecamatan,
            $model->kelurahan,
            $model->alamat_lengkap,
            $model->no_rt,
            $model->no_rw,
            $model->no_hp,
            $model->kunjungan_ke,
            Carbon::parse($model->created_at)->format('Y-m-d'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER,
            'T' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function setTitle()
    {
        $this->title = "Registrasi Mandiri";
    }

    public function setCellRange()
    {
        $this->cellRange = "V";
    }

    public function setHeader()
    {
        $this->header = [
            'No',
            'No Registrasi',
            'Kode Sampel',
            'Kategori',
            'Status',
            'Nama Pasien',
            'NIK',
            'Usia',
            'Satuan',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Provinsi',
            'Kota',
            'Kecamatan',
            'Kelurahan',
            'Alamat',
            'RT',
            'RW',
            'No. HP',
            'Kunjungan Ke',
            'Tanggal Registrasi',
        ];
    }
}
