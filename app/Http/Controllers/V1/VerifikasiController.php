<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifikasiSampelBulkRequest;
use App\Http\Requests\VerifikasiUpdateToVerifiedRequest;
use App\Http\Resources\SampelDetailResource;
use App\Models\PemeriksaanSampel;
use App\Models\Sampel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VerifikasiController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Models\Sampel $sampel
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Sampel $sampel)
    {
        return new SampelDetailResource($sampel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sampel       $sampel
     *
     * @return \Illuminate\Http\Response
     */
    public function updateToVerified(VerifikasiUpdateToVerifiedRequest $request, Sampel $sampel)
    {
        abort_if(!$sampel->nomor_register, Response::HTTP_BAD_REQUEST, 'Nomor registrasi belum terdaftar');
        $pemeriksaanSampel = PemeriksaanSampel::find($request->input('last_pemeriksaan_id'));
        $pemeriksaanSampel->fill($request->validated());
        $pemeriksaanSampel->save();
        $this->updateSampel($sampel, $request->user(), $pemeriksaanSampel);
        return response()->json(['message' => 'success']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sampel       $sampel
     *
     * @return \Illuminate\Http\Response
     */
    public function verifiedSingleSampel(Request $request, Sampel $sampel)
    {
        abort_if(!$sampel->nomor_register, Response::HTTP_BAD_REQUEST, 'Nomor registrasi belum terdaftar');
        $this->updateSampel($sampel, $request->user(), $sampel);
        return response()->json(['message' => 'success']);
    }

    public function verifiedBulkSampel(VerifikasiSampelBulkRequest $request)
    {
        $user = $request->user();
        DB::beginTransaction();
        try {
            Sampel::whereIn('id', $request->input('id'))->chunk(100, function ($sampels) use ($user) {
                foreach ($sampels as $sampel) {
                    abort_if(!$sampel->nomor_register, Response::HTTP_BAD_REQUEST, 'Nomor registrasi belum terdaftar');
                    $this->updateSampel($sampel, $user, $sampel);
                }
            });
            DB::commit();
            return response()->json(['message' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function updateSampel($sampel, $user, $metadata)
    {
        $sampel->addLog([
            'user_id' => $user->id,
            'metadata' => $metadata,
            'description' => 'Data pasien dan sampel terverifikasi',
        ], 'sample_verified');

        $sampel->update([
            'sampel_status' => 'sample_verified',
            'waktu_sample_verified' => now(),
        ]);
    }
}
