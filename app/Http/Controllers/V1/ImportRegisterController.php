<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportDataRegisterRujukan;
use App\Http\Requests\ImportExcelRequest;
use App\Imports\RegisterMandiriImport;
use App\Imports\RegisterRujukanImport;
use App\Imports\RegisterRujukanTesMasifImport;
use App\Models\Pasien;
use App\Models\PengambilanSampel;
use App\Models\Register;
use App\Models\Sampel;
use App\Models\TesMasif;
use App\Traits\RegisterTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ImportRegisterController extends Controller
{
    use RegisterTrait;

    /**
     * Import Register Mandiri
     *
     */
    public function importRegisterMandiri(ImportExcelRequest $request)
    {
        Excel::import(new RegisterMandiriImport(), $request->file('register_file'));
        return response()->json(['message' => 'Sukses import data.']);
    }

    /**
     * Import Register Rujukan
     */
    public function importRegisterRujukan(ImportExcelRequest $request)
    {
        $import = new RegisterRujukanImport();
        $import->import($request->file('register_file'));
        return response()->json($import->result, $import->getStatusCodeResponse());
    }

    public function importRegisterRujukanTesMasif(ImportExcelRequest $request)
    {
        $import = new RegisterRujukanTesMasifImport();
        $import->import($request->file('register_file'));
        return response()->json($import->result, $import->getStatusCodeResponse());
    }

    public function importDataRegisterRujukan(ImportDataRegisterRujukan $request)
    {
        DB::beginTransaction();
        try {
            $dataChunk = array_chunk($request->input('data'), 50);
            foreach ($dataChunk as $data) {
                $this->saveDataRegisterRujukan($data);
            }
            DB::commit();
            return response()->json(['message' => 'Sukses import data.']);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    private function saveDataRegisterRujukan(array $data)
    {
        foreach ($data as $row) {
            $row['creator_user_id'] = Auth::user()->id;
            $registerData = [
                'register_uuid' => Str::uuid(),
                'nomor_register' => $this->generateNomorRegister(null, $row['jenis_registrasi']),
            ];
            $register = Register::create($row + $registerData);
            $pasien = Pasien::create($row);
            $register->pasiens()->attach($pasien);
            $sampel = Sampel::firstOrNew(['nomor_sampel' => $row['nomor_sampel']]);
            $pengambilanSampel = PengambilanSampel::create(['catatan' => $row['keterangan_lain']]);
            $sampel->fill([
                'pengambilan_sampel_id' => $pengambilanSampel->id,
                'waktu_sample_taken' => $sampel->waktu_sample_taken ?? now(),
                'sampel_status' => $sampel->sampel_status ?? 'sample_taken',
                'nomor_register' => $register->nomor_register,
                'register_id' => $register->id,
            ]);
            $sampel->save();
            if (isset($row['id'])) {
                TesMasif::find($row['id'])->update(['available' => false]);
            }
        }
    }
}
