<?php

namespace App\Traits;

use App\Models\JenisSampel;
use App\Models\Sampel;
use App\Enums\JenisSampelEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

/**
 * Sampel trait
 *
 */
trait SampelTrait
{
    /**
     * updateState
     *
     * @param  Sampel $sampel
     * @param  String $sampel_status
     * @param  Json $metaData
     * @param  Text $description
     * @return void
     */
    public function updateState($sampel, $sampel_status, $metaData, $description)
    {
        $sampel->updateState($sampel_status, [
            'user_id' => Auth::user()->id,
            'metadata' => $metaData,
            'description' => $description,
        ]);
    }

    public function saveSampelRequest($request)
    {
        $jenis_sampel = $request->input('sam_namadiluarjenis');
        if ($request->input('sam_jenis_sampel') != JenisSampelEnum::luarJenis()->getIndex()) {
            $jenis_sampel = JenisSampel::find($request->input('sam_jenis_sampel'))->nama;
        }

        return [
            'penerima_sampel' => $request->pen_penerima_sampel,
            'catatan' => $request->pen_catatan,
            'sumber_sampel' => $request->pen_sampel_sumber,
            'nomor_sampel' => $request->nomorsampel,
            'jenis_sampel_id' => $request->sam_jenis_sampel,
            'jenis_sampel_nama' => $jenis_sampel,
            'tanggal_pengambilan_sampel' => $request->tanggalsampel,
            'jam_pengambilan_sampel' => $request->pukulsampel,
            'petugas_pengambilan_sampel' => $request->petugas_pengambil,
            'waktu_sample_taken' => Carbon::parse($request->tanggalsampel)->format('Y-m-d') . ' ' . $request->pukulsampel,
            'jenis_vtm' => $request->vtm,
            'sampel_diterima' => true
        ];
    }

    public function searchAdminSampel($record, $search)
    {
        $record->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nomor_sampel', 'ilike', '%' . $search . '%')
                   ->orWhere('nomor_register', 'ilike', '%' . $search . '%');
            });
        });
        return $record;
    }
}
