<?php

namespace App\Http\Controllers\V1;

use App\Events\GenerateLetterSampleEvent;
use App\Events\GenerateLetterSwabAntigenEvent;
use App\Http\Controllers\Controller;
use App\Models\Sampel;
use App\Models\SwabAntigen;
use App\Traits\GeneratePDFTrait;

class ExportPDFController extends Controller
{
    use GeneratePDFTrait;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadPdfHasil(Sampel $sampel)
    {
        return $this->downloadPDF($sampel);
    }

    public function regeneratePdfHasil(Sampel $sampel)
    {
        event(new GenerateLetterSampleEvent($sampel));
        return response()->json(['message' => 'success']);
    }

    public function regeneratePdfSwabAntigen(SwabAntigen $swab_antigen)
    {
        event(new GenerateLetterSwabAntigenEvent($swab_antigen));
        return response()->json(['message' => 'success']);
    }

    public function downloadPdfSwabAntigen(SwabAntigen $swab_antigen)
    {
        return $this->downloadPDF($swab_antigen);
    }
}
