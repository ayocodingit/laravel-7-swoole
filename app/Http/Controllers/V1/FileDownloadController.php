<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $namaFile = $request->input('namaFile', '');
        $fullNameFile = "$namaFile.xlsx";
        $storagePath = $this->getStoragePathFile($namaFile);
        abort_if(Storage::missing($storagePath), Response::HTTP_BAD_REQUEST, 'File not exists!');
        return Storage::download($storagePath, $fullNameFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'inline; filename="' . $fullNameFile . '"'
        ]);
    }

    private function getStoragePathFile($namaFile)
    {
        $storagePath = "template/labkes/$namaFile.xlsx";
        if (in_array($namaFile, ['fasyankes', 'labSatelit', 'wilayah'])) {
            $storagePath = "template/data_master/$namaFile.xlsx";
        }
        return $storagePath;
    }
}
