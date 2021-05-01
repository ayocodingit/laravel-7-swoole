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

class VerifikasiExport implements
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
            $model->nama_kota,
            $this->alamatLengkap($model),
            $model->no_rt,
            $model->no_rw,
            $model->no_hp,
            optional($this->convertEnumFasyankesPengirim($model->fasyankes_pengirim))->getValue(),
            $model->fasyankes_nama,
            $model->jenis_sampel_nama,
            $this->hasil_deteksi($model->hasil_deteksi),
            $this->getHasilDeteksiTerkecil($model->hasil_deteksi),
            optional($this->convertEnumKesimpulanPemeriksaan($model->kesimpulan_pemeriksaan))->getValue(),
            Carbon::parse($model->created_at)->format('Y-m-d'),
            optional($model->waktu_sample_taken)->format('Y-m-d'),
            $model->tanggal_input_hasil,
            optional(Carbon::parse($model->tanggal_mulai_ekstraksi))->format('Y-m-d'),
            optional($model->waktu_sample_valid)->format('Y-m-d'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER,
            'R' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function setTitle()
    {
        $this->title = "Verifikasi";
    }

    public function setCellRange()
    {
        $this->cellRange = "AB";
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
            'Domisili',
            'Alamat',
            'RT',
            'RW',
            'No Telp',
            'Instansi Pengirim',
            'Nama Fasyankes/Dinkes',
            'Tipe Sampel',
            'Parameter Lab',
            'CT Hasil',
            'Hasil',
            'Tanggal Registrasi',
            'Tanggal Terima Sampel',
            'Tanggal Pemeriksaan',
            'Tanggal Mulai Ekstraksi',
            'Tanggal Validasi',
        ];
    }
}
