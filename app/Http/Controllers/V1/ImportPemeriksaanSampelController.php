<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportExcelRequest;
use App\Imports\HasilPemeriksaanImport;
use App\Models\PemeriksaanSampel;
use App\Models\Sampel;
use App\Traits\SampelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportPemeriksaanSampelController extends Controller
{
    use SampelTrait;

    public function importHasilPemeriksaan(ImportExcelRequest $request)
    {
        $importer = new HasilPemeriksaanImport();
        Excel::import($importer, $request->file('register_file'));

        return response()->json([
            'status' => 200,
            'message' => 'Sukses membaca file import excel',
            'data' => $importer->data,
            'errors' => $importer->errors,
            'errors_count' => $importer->errors_count,
        ]);
    }

    public function importDataHasilPemeriksaan(Request $request)
    {
        $user = $request->user();
        DB::beginTransaction();
        try {
            foreach ($request->input('data') as $row) {
                $sampel = Sampel::findOrFail($row['sampel_id']);
                $pemeriksaanSampel = PemeriksaanSampel::firstOrNew(['sampel_id' => $sampel->id]);
                $pemeriksaanSampel->sampel_id = $sampel->id;
                $pemeriksaanSampel->user_id = $pemeriksaanSampel->user_id ?? $user->id;
                $pemeriksaanSampel->tanggal_input_hasil = $row['tanggal_input_hasil'];
                $pemeriksaanSampel->tanggal_mulai_pemeriksaan = $row['tanggal_input_hasil'];
                $pemeriksaanSampel->jam_input_hasil = '12:00';
                $pemeriksaanSampel->catatan_pemeriksaan = '';
                $pemeriksaanSampel->hasil_deteksi = $row['target_gen'];
                $pemeriksaanSampel->kesimpulan_pemeriksaan = $row['kesimpulan_pemeriksaan'];
                $pemeriksaanSampel->save();
                $this->updateSampel($sampel, $pemeriksaanSampel, $user, $row);
            }
            DB::commit();
            return response()->json(['message' => 'Sukses import data.']);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    private function updateSampel($sampel, $pemeriksaanSampel, $user, $row)
    {
        $description = 'Analisis Selesai, hasil ' . strtoupper($pemeriksaanSampel->kesimpulan_pemeriksaan);
        if ($sampel->sampel_status == 'pcr_sample_received' || $sampel->sampel_status == 'extraction_sample_sent') {
            $this->updateState($sampel, 'pcr_sample_analyzed', $pemeriksaanSampel, $description);
        } else {
            $sampel->addLog([
                'user_id' => $user->id,
                'metadata' => $pemeriksaanSampel,
                'description' => $description,
            ]);
        }
        $sampel->waktu_pcr_sample_analyzed = $row['tanggal_input_hasil'];
        $sampel->save();
    }
}
