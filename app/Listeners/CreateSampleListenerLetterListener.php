<?php

namespace App\Listeners;

use App\Events\GenerateLetterSampleEvent;
use App\Events\GeneratePdfSampleEvent;
use App\Models\Sampel;
use App\Traits\ConvertEnumTrait;
use App\Traits\ExportMappingTrait;
use App\Traits\GeneratePDFTrait;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Carbon;

class CreateSampleListenerLetterListener
{
    use GeneratePDFTrait;
    use ExportMappingTrait;
    use ConvertEnumTrait;

    private $timestamp;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->timestamp = now()->timestamp;
    }

    /**
     * Handle the event.
     *
     * @param GeneratePdfSampleEvent $event
     */
    public function handle(GenerateLetterSampleEvent $event)
    {
        $sampel = $event->sampel;
        $pdfFile = $this->createPDF($sampel);
        $fileName = "SURAT_HASIL_{$sampel->no_hasil}_{$this->timestamp}.pdf";
        return $this->savePDF($sampel, $pdfFile, $fileName);
    }

    public function createPDF(Sampel $sampel)
    {
        $pemeriksaanSampel = $sampel->pemeriksaanSampel;
        $pasien = $sampel->register->pasiens()->first();
        $data['nomor_sampel'] = $sampel->nomor_sampel;
        $data['pasien'] = $pasien;
        $data['validator'] = $sampel->validator;
        $data['hasil_pemeriksaan'] = $this->convertEnumKesimpulanPemeriksaan($pemeriksaanSampel->kesimpulan_pemeriksaan)->getValue();
        $data['ct_normal'] = $pemeriksaanSampel->ct_normal;
        $data['kop_surat'] = $this->getKopSurat();
        $data['umur_pasien'] = $this->hitungUmur($pasien);
        $data['ct_value'] = $this->getHasilDeteksiTerkecil($sampel->hasil_deteksi);
        $data['register'] = $sampel->register;
        $data['tanggal_registrasi'] = $this->formatTanggalIndo($sampel->waktu_sample_taken);
        $data['tanggal_periksa'] = $this->formatTanggalIndo($pemeriksaanSampel->tanggal_mulai_pemeriksaan);
        $data['logo_watermark'] = $this->getLogoWatermark();
        $pdf = PDF::loadView('pdf_templates.print_validasi', $data);
        $pdf->setPaper([0, 0, 609.4488, 935.433]);

        return $pdf->download()->getOriginalContent();
    }

    public function formatTanggalIndo($date)
    {
        return Carbon::parse($date)->translatedFormat('d F Y');
    }
}
