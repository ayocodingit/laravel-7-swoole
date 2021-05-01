<?php

namespace App\Listeners;

use App\Events\GenerateLetterSwabAntigenEvent;
use App\Models\SwabAntigen;
use App\Traits\GeneratePDFTrait;
use Barryvdh\DomPDF\Facade as PDF;

class CreateAntigenSwabLetterListener
{
    use GeneratePDFTrait;

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
     * @param  object  $event
     * @return void
     */
    public function handle(GenerateLetterSwabAntigenEvent $event)
    {
        $swab_antigen = $event->swab_antigen;
        $pdfFile = $this->createPDF($swab_antigen);
        $fileName = "SURAT_HASIL_{$swab_antigen->no_hasil}_{$this->timestamp}.pdf";
        return $this->savePDF($swab_antigen, $pdfFile, $fileName);
    }

    public function createPDF(SwabAntigen $swab_antigen)
    {
        $data['swab_antigen'] = $swab_antigen;
        $data['umur_pasien'] = $this->hitungUmur($swab_antigen);
        $data['validator'] = $swab_antigen->validator;
        $data['kop_surat'] = $this->getKopSurat();
        $data['logo_watermark'] = $this->getLogoWatermark();
        $pdf = PDF::loadView('pdf_templates.print_swab_antigen', $data);
        $pdf->setPaper([0, 0, 609.4488, 935.433]);
        return $pdf->download()->getOriginalContent();
    }
}
