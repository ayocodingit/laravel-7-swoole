<?php

namespace App\Http\Controllers\V1;

use App\Enums\LabPCREnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditEkstraksiRequest;
use App\Http\Requests\EkstraksiKirimRequest;
use App\Http\Requests\EkstraksiKirimUlangRequest;
use App\Http\Requests\TerimaEkstraksiRequest;
use App\Http\Resources\EkstraksiDetailResource;
use Illuminate\Http\Request;
use App\Models\Sampel;
use App\Models\Ekstraksi;
use App\Models\PemeriksaanSampel;
use App\Models\LabPCR;
use App\Traits\SampelTrait;
use Illuminate\Support\Arr;

class EkstraksiController extends Controller
{
    use SampelTrait;

    public function detail(Sampel $sampel)
    {
        return new EkstraksiDetailResource($sampel);
    }

    public function edit(EditEkstraksiRequest $request, Sampel $sampel)
    {
        $user = $request->user();
        $lab_pcr = LabPCR::find($request->lab_pcr_id);
        $ekstraksi = $sampel->ekstraksi;
        $ekstraksi->user_id = $ekstraksi->user_id ?? $user->id;
        $ekstraksi->sampel_id = $sampel->id;
        $ekstraksi->fill($request->validated());
        $ekstraksi->save();
        if ($sampel->sampel_status != 'extraction_sample_extracted') {
            $sampel->lab_pcr_id = $request->input('lab_pcr_id');
            $sampel->lab_pcr_nama = $lab_pcr->id == LabPCREnum::lainnya()->getIndex() ? $request->input('lab_pcr_nama') : $lab_pcr->nama;
            $tanggalEkstraksi = $ekstraksi->tanggal_mulai_ekstraksi->format('Y-m-d');
            $sampel->waktu_extraction_sample_extracted = $tanggalEkstraksi . ' ' . $ekstraksi->jam_mulai_ekstraksi;
            $tanggalPengiriman = $ekstraksi->tanggal_pengiriman_rna->format('Y-m-d');
            $sampel->waktu_extraction_sample_sent = $tanggalPengiriman . ' ' . $ekstraksi->jam_pengiriman_rna;
            $sampel->save();
        }
        return response()->json(['message' => 'Perubahan berhasil disimpan']);
    }

    public function terima(TerimaEkstraksiRequest $request)
    {
        $user = $request->user();
        $samples = Sampel::whereIn('nomor_sampel', Arr::pluck($request->samples, 'nomor_sampel'))->get();
        foreach ($samples as $sampel) {
            $ekstraksi = Ekstraksi::firstOrNew(['sampel_id' => $sampel->id]);
            $ekstraksi->user_id = $ekstraksi->user_id ?? $user->id;
            $ekstraksi->sampel_id = $sampel->id;
            $ekstraksi->fill($request->validated());
            $ekstraksi->save();
            $tanggalEkstraksi = $ekstraksi->tanggal_mulai_ekstraksi->format('Y-m-d');
            $sampel->waktu_extraction_sample_extracted = $tanggalEkstraksi . ' ' . $ekstraksi->jam_mulai_ekstraksi;
            $this->updateState($sampel, 'extraction_sample_extracted', $ekstraksi, 'Sampel diekstraksi');
        }
        return response()->json(['message' => 'Penerimaan sampel berhasil dicatat']);
    }

    public function setInvalid(Request $request, Sampel $sampel)
    {
        if (!in_array($sampel->sampel_status, ['extraction_sample_reextract', 'sample_taken'])) {
            return $this->getResponseError($sampel, 'invalid');
        }
        $user = $request->user();
        $ekstraksi = $sampel->ekstraksi;
        $ekstraksi->user_id = $user->id;
        $ekstraksi->catatan_pengiriman = $request->input('alasan');
        $ekstraksi->save();
        $this->updateState($sampel, 'sample_invalid', $ekstraksi, 'Sample marked as invalid');
        return response()->json(['message' => 'Sampel berhasil ditandai sebagai invalid']);
    }

    public function setProses(Request $request, Sampel $sampel)
    {
        if ($sampel->sampel_status != 'extraction_sample_reextract') {
            return $this->getResponseError($sampel, 'proses');
        }
        $user = $request->user();
        $ekstraksi = $sampel->ekstraksi;
        $ekstraksi->user_id = $user->id;
        $ekstraksi->catatan_pengiriman = $request->input('alasan');
        $ekstraksi->save();
        $this->updateState($sampel, 'extraction_sample_extracted', $ekstraksi, 'Sampel diekstraksi');
        return response()->json(['message' => 'Sampel berhasil ditandai sebagai sampel proses']);
    }

    public function kirim(EkstraksiKirimRequest $request)
    {
        $this->ekstraksiKirim($request);
        return response()->json(['message' => 'Pengiriman sampel berhasil dicatat']);
    }

    public function kirimUlang(EkstraksiKirimUlangRequest $request)
    {
        $this->ekstraksiKirim($request);
        return response()->json(['message' => 'Pengiriman ulang sampel berhasil dicatat']);
    }

    public function ekstraksiKirim($request)
    {
        $user = $request->user();
        $samples = Sampel::whereIn('nomor_sampel', Arr::pluck($request->samples, 'nomor_sampel'))->get();
        $lab_pcr = LabPCR::find($request->lab_pcr_id);
        foreach ($samples as $sampel) {
            $ekstraksi = $sampel->ekstraksi;
            $ekstraksi->user_id = $user->id;
            $ekstraksi->sampel_id = $sampel->id;
            $ekstraksi->fill($request->validated());
            $ekstraksi->save();
            $sampel->lab_pcr_id = $request->lab_pcr_id;
            $sampel->lab_pcr_nama = $lab_pcr->id == LabPCREnum::lainnya()->getIndex() ? $request->input('lab_pcr_nama') : $lab_pcr->nama;
            $sampel->waktu_extraction_sample_sent = $ekstraksi->tanggal_pengiriman_rna->format('Y-m-d') . ' ' . $ekstraksi->jam_pengiriman_rna;
            $this->updateState($sampel, 'extraction_sample_sent', $ekstraksi, 'RNA dikirim ke lab PCR');
        }
    }

    public function musnahkan(Request $request, Sampel $sampel)
    {
        $user = $request->user();
        $sampel->is_musnah_ekstraksi = true;
        $sampel->addLog([
            'user_id' => $user->id,
            'metadata' => $sampel,
            'description' => 'Sample marked as destroyed at extraction chamber',
        ]);
        $sampel->save();
        return response()->json(['message' => 'Sampel berhasil ditandai telah dimusnahkan']);
    }

    public function setKurang(Request $request, Sampel $sampel)
    {
        $user = $request->user();
        if ($sampel->sampel_status != 'sample_invalid') {
            return $this->getResponseError($sampel, 'sampel kurang');
        }
        $pemeriksaanSampel = PemeriksaanSampel::firstOrNew(['sampel_id' => $sampel->id]);
        $pemeriksaanSampel->user_id = $user->id;
        $pemeriksaanSampel->sampel_id = $sampel->id;
        $pemeriksaanSampel->kesimpulan_pemeriksaan = 'sampel kurang';
        $pemeriksaanSampel->save();
        $this->updateState($sampel, 'pcr_sample_analyzed', $pemeriksaanSampel, 'Sample marked as insufficient');
        return response()->json(['message' => 'Sampel berhasil ditandai sebagai sampel kurang']);
    }

    public function setSwabUlang(Request $request, Sampel $sampel)
    {
        $user = $request->user();
        $pemeriksaanSampel = PemeriksaanSampel::firstOrNew(['sampel_id' => $sampel->id]);
        $pemeriksaanSampel->user_id = $pemeriksaanSampel->user_id ?? $user->id;
        $pemeriksaanSampel->sampel_id = $sampel->id;
        $pemeriksaanSampel->kesimpulan_pemeriksaan = 'swab_ulang';
        $pemeriksaanSampel->catatan_pemeriksaan = $request->input('alasan');
        $pemeriksaanSampel->save();
        $this->updateState($sampel, 'swab_ulang', $pemeriksaanSampel, 'Sample marked as need to be re-swab');
        return response()->json(['message' => 'Sampel berhasil ditandai sebagai sampel yang perlu swab ulang']);
    }

    private function getResponseError($sampel, $tahapan)
    {
        $message = "Status sampel sudah pada tahap {$sampel->status->deskripsi}
                    sehingga tidak dapat ditandai sebagai {$tahapan}";
        return response()->json(['message' => $message], 422);
    }
}
