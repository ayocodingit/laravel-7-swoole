<?php

namespace App\Traits;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait GeneratePDFTrait
{
    public function getKopSurat()
    {
        $pathDirectory = 'kop_surat/kop-surat-labkesda.png';
        $image = public_path($pathDirectory);
        abort_if(!file_exists($image), Response::HTTP_NOT_FOUND);
        $imageContent = file_get_contents($image);
        return 'data:image/png;base64, ' . base64_encode($imageContent);
    }

    public function getLogoWatermark()
    {
        $pathDirectory = 'kop_surat/logo_watermark.png';
        $image = public_path($pathDirectory);
        abort_if(!file_exists($image), Response::HTTP_NOT_FOUND);
        $imageContent = file_get_contents($image);
        return 'data:image/png;base64, ' . base64_encode($imageContent);
    }

    public function createFile($fileName)
    {
        $dataFile['mime_type'] = 'application/pdf';
        $dataFile['extension'] = 'pdf';
        $dataFile['original_name'] = $fileName;
        return File::create($dataFile);
    }

    public function uploadFile($filePath, $file)
    {
        return Storage::put($filePath, $file);
    }

    public function isDoesntExistFile($filePath)
    {
        return Storage::missing($filePath);
    }

    public function getFilePath($model)
    {
        $filePath = $model->storage_path . DIRECTORY_SEPARATOR . optional($model->validFile)->original_name;
        $isValidFile = !$model->validFile && !$this->isDoesntExistFile($filePath);
        abort_if($isValidFile, Response::HTTP_NOT_FOUND);
        return $filePath;
    }

    public function downloadPDF($model)
    {
        $filePath = $this->getFilePath($model);
        $model->fill([
            'counter_print_hasil' => ++$model->counter_print_hasil,
            'waktu_sample_print' => Carbon::now(),
            'waktu_sampel_print' => Carbon::now()
        ])->save();
        return Storage::download($filePath, $model->validFile->original_name . '.pdf', [
            "Content-Type" => $model->validFile->mime_type,
            "Content-Description" => 'File Transfer',
            "Content-Disposition" => 'attachment;filename=' . $model->validFile->original_name . '.pdf',
        ]);
    }

    public function savePDF($model, $file, $fileName)
    {
        $filePath = $model->storage_path . DIRECTORY_SEPARATOR . $fileName;
        DB::beginTransaction();
        try {
            $this->uploadFile($filePath, $file);
            $newFile = $this->createFile($fileName);
            $model->fill(['valid_file_id' => $newFile->id])->save();
            DB::commit();
            return;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function hitungUmur($model)
    {
        if ($model->usia_tahun) {
            return $model->usia_tahun;
        } elseif ($model->tanggal_lahir) {
            return Carbon::parse($model->tanggal_lahir)->diff(Carbon::now())->format('%y');
        }
        return '-';
    }
}
