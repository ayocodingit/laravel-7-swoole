<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TerimaPCRRequest;
use Illuminate\Http\Request;
use App\Models\Sampel;
use App\Models\PemeriksaanSampel;
use App\Models\LabPCR;
use Validator;
use Illuminate\Support\Arr;
use App\Traits\PemeriksaanTrait;

class PCRController extends Controller
{
    use PemeriksaanTrait;

    public function detail(Request $request, $id)
    {
        $model = Sampel::with(['pcr', 'status', 'ekstraksi', 'pemeriksaanSampel'])->find($id);
        $model->log_pcr = $model->logs()
            ->whereIn('sampel_status', ['pcr_sample_received', 'pcr_sample_analyzed', 'extraction_sample_reextract'])
            ->orderByDesc('created_at')
            ->get();
        $model['re_pcr'] = Sampel::find($id)->logs->where('sampel_status', 'pcr_sample_received')->count() >= 2 ? 're-PCR' : null;

        return response()->json(['status' => 200, 'message' => 'success', 'data' => $model]);
    }

    public function edit(Request $request, $id)
    {
        $user = $request->user();
        $sampel = Sampel::with(['pcr'])->find($id);
        if ($sampel->sampel_status == 'pcr_sample_received') {
            $v = Validator::make($request->all(), [
                'tanggal_penerimaan_sampel' => 'required',
                'jam_penerimaan_sampel' => 'required',
                'petugas_penerima_sampel' => 'required',
                'catatan_penerimaan' => 'nullable',
                'operator_ekstraksi' => 'required',
                'tanggal_mulai_ekstraksi' => 'required',
                'jam_mulai_ekstraksi' => 'required',
                'metode_ekstraksi' => 'required',
                'nama_kit_ekstraksi' => 'required',
            ]);
        } else {
            $v = Validator::make($request->all(), [
                'tanggal_penerimaan_sampel' => 'required',
                'jam_penerimaan_sampel' => 'required',
                'petugas_penerima_sampel' => 'required',
                'operator_ekstraksi' => 'required',
                'tanggal_mulai_ekstraksi' => 'required',
                'jam_mulai_ekstraksi' => 'required',
                'metode_ekstraksi' => 'required',
                'nama_kit_ekstraksi' => 'required',
                'tanggal_pengiriman_rna' => 'required',
                'jam_pengiriman_rna' => 'required',
                'nama_pengirim_rna' => 'required',
                'lab_pcr_id' => 'required',
                'catatan_pengiriman' => 'nullable',
                'lab_pcr_nama' => 'required_if:lab_pcr_id,999999',
            ]);
        }
        $lab_pcr = LabPCR::find($request->lab_pcr_id);

        if (!$lab_pcr) {
            $v->after(function ($validator) {
                $validator->errors()->add('samples', 'Lab PCR Tidak ditemukan');
            });
        }

        $v->validate();

        $pcr = $sampel->pcr;
        if (!$pcr) {
            $pcr = new PemeriksaanSampel();
            $pcr->sampel_id = $sampel->id;
            $pcr->user_id = $user->id;
        }
        $pcr->tanggal_penerimaan_sampel = parseDate($request->tanggal_penerimaan_sampel);
        $pcr->jam_penerimaan_sampel = parseTime($request->jam_penerimaan_sampel);
        $pcr->petugas_penerima_sampel = $request->petugas_penerima_sampel;
        $pcr->operator_ekstraksi = $request->operator_ekstraksi;
        $pcr->tanggal_mulai_ekstraksi = parseDate($request->tanggal_mulai_ekstraksi);
        $pcr->jam_mulai_ekstraksi = parseTime($request->jam_mulai_ekstraksi);
        $pcr->metode_ekstraksi = $request->metode_ekstraksi;
        $pcr->nama_kit_ekstraksi = $request->nama_kit_ekstraksi;
        $pcr->tanggal_pengiriman_rna = parseDate($request->tanggal_pengiriman_rna);
        $pcr->jam_pengiriman_rna = parseTime($request->jam_pengiriman_rna);
        $pcr->nama_pengirim_rna = $request->nama_pengirim_rna;
        $pcr->catatan_penerimaan = $request->catatan_penerimaan;
        $pcr->catatan_pengiriman = $request->catatan_pengiriman;
        $pcr->save();

        $sampel->lab_pcr_id = $request->lab_pcr_id;
        $sampel->lab_pcr_nama = $lab_pcr->id == 999999 ? $request->lab_pcr_nama : $lab_pcr->nama;
        $sampel->waktu_pcr_sample_received = $pcr->tanggal_mulai_ekstraksi ? date('Y-m-d H:i:s', strtotime($pcr->tanggal_mulai_ekstraksi . ' ' . $pcr->jam_mulai_ekstraksi)) : null;
        $sampel->waktu_extraction_sample_sent = $pcr->tanggal_pengiriman_rna ? date('Y-m-d H:i:s', strtotime($pcr->tanggal_pengiriman_rna . ' ' . $pcr->jam_pengiriman_rna)) : null;
        $sampel->save();

        return response()->json(['status' => 201, 'message' => 'Perubahan berhasil disimpan']);
    }

    public function invalid(Request $request, $id)
    {
        $user = $request->user();
        $sampel = Sampel::with(['pcr'])->find($id);
        $v = Validator::make($request->all(), [
            'catatan_pemeriksaan' => 'required',
        ]);

        $v->validate();

        $pcr = $sampel->pcr;
        if (!$pcr) {
            $pcr = new PemeriksaanSampel();
            $pcr->sampel_id = $sampel->id;
            $pcr->user_id = $user->id;
        }
        $pcr->catatan_pemeriksaan = $request->catatan_pemeriksaan;
        $pcr->save();

        $sampel->updateState('extraction_sample_reextract', [
            'user_id' => $user->id,
            'metadata' => $pcr,
            'description' => 'Invalid PCR, need re-extraction',
        ]);

        return response()->json(['status' => 201, 'message' => 'Perubahan berhasil disimpan']);
    }

    public function input(Request $request, $id)
    {
        $user = $request->user();
        $sampel = Sampel::with(['pcr'])->find($id);
        $v = Validator::make($request->all(), [
            'kesimpulan_pemeriksaan' => 'required',
            'hasil_deteksi.*.target_gen' => 'required',
            'hasil_deteksi.*.ct_value' => 'required',
        ]);
        if (count($request->hasil_deteksi) < 1) {
            $v->after(function ($validator) {
                $validator->errors()->add('samples', 'Minimal 1 hasil deteksi CT Value');
            });
        }

        $v->validate();

        $pcr = $sampel->pcr;
        if (!$pcr) {
            $pcr = new PemeriksaanSampel();
            $pcr->sampel_id = $sampel->id;
            $pcr->user_id = $user->id;
        }
        $pcr->tanggal_input_hasil = $request->tanggal_input_hasil;
        $pcr->jam_input_hasil = $request->jam_input_hasil;
        $pcr->catatan_pemeriksaan = $request->catatan_pemeriksaan;
        $pcr->grafik = $request->grafik;
        $pcr->hasil_deteksi = $this->parseHasilDeteksi($request->hasil_deteksi);
        $pcr->kesimpulan_pemeriksaan = $request->kesimpulan_pemeriksaan;
        $pcr->save();

        if ($sampel->sampel_status == 'pcr_sample_received') {
            $new_sampel_status = 'pcr_sample_analyzed';
            if ($pcr->kesimpulan_pemeriksaan == 'inkonklusif') {
                $new_sampel_status = 'inkonklusif';
            }
            $sampel->updateState($new_sampel_status, [
                'user_id' => $user->id,
                'metadata' => $pcr,
                'description' => 'Analisis Selesai, hasil ' . strtoupper($pcr->kesimpulan_pemeriksaan),
            ]);
        } else {
            $sampel->addLog([
                'user_id' => $user->id,
                'metadata' => $pcr,
                'description' => 'Analisis Selesai, hasil ' . strtoupper($pcr->kesimpulan_pemeriksaan),
            ]);
            $sampel->waktu_pcr_sample_analyzed = date('Y-m-d H:i:s');
            $sampel->save();
        }

        return response()->json(['status' => 201, 'message' => 'Hasil analisa berhasil disimpan']);
    }

    /**
     * terima sampel PCR
     *
     * @param  mixed $request
     * @return void
     */
    public static function terima(TerimaPCRRequest $request)
    {
        $user = $request->user();
        $samples = Sampel::whereIn('nomor_sampel', Arr::pluck($request->samples, 'nomor_sampel'))->get();
        foreach ($samples as $sampel) {
            $pemeriksaanSampel = PemeriksaanSampel::firstOrNew(['sampel_id' => $sampel->id]);
            $pemeriksaanSampel->user_id = $pemeriksaanSampel->user_id ?? $user->id;
            $pemeriksaanSampel->sampel_id = $sampel->id;
            $pemeriksaanSampel->fill($request->validated());
            $pemeriksaanSampel->save();

            $sampel->waktu_pcr_sample_received = $pemeriksaanSampel->tanggal_mulai_ekstraksi . ' ' . $pemeriksaanSampel->jam_mulai_ekstraksi;
            $sampel->updateState('pcr_sample_received', [
                'user_id' => $user->id,
                'metadata' => $pemeriksaanSampel,
                'description' => 'RNA dianalisis oleh mesin PCR',
            ]);
        }
        return response()->json(['message' => 'Penerimaan sampel berhasil dicatat'], 200);
    }

    public function musnahkan(Request $request, $id)
    {
        $user = $request->user();
        $sampel = Sampel::with(['status', 'pcr'])->find($id);
        if (!$sampel) {
            return response()->json(['success' => false, 'code' => 422, 'message' => 'Sampel tidak ditemukan'], 422);
        }
        $pcr = $sampel->pcr;
        if (!$pcr) {
            $pcr = new PemeriksaanSampel();
            $pcr->sampel_id = $sampel->id;
            $pcr->user_id = $user->id;
        }
        $sampel->is_musnah_pcr = true;
        $sampel->save();

        $sampel->addLog([
            'user_id' => $user->id,
            'metadata' => $pcr,
            'description' => 'Sample marked as destroyed at PCR chamber',
        ]);

        return response()->json(['success' => true, 'code' => 201, 'message' => 'Sampel berhasil ditandai telah dimusnahkan']);
    }
}
