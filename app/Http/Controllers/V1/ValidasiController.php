<?php

namespace App\Http\Controllers\V1;

use App\Exports\ValidasiExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidasiSampelBulkRequest;
use App\Http\Requests\ValidasiSampelRequest;
use App\Http\Resources\SampelDetailResource;
use App\Http\Resources\SampelResource;
use App\Models\PemeriksaanSampel;
use App\Models\Sampel;
use App\Models\Validator;
use App\Traits\ExportMappingTrait;
use App\Traits\PaginateTrait;
use App\Traits\ValidasiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ValidasiController extends Controller
{
    use ExportMappingTrait;
    use ValidasiTrait;
    use PaginateTrait;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params          = $this->getValidParams($request);
        $perpage         = $this->getValidPerpage($request->input('perpage'));
        $order_direction = $this->getValidOrderDirection($request->input('order_direction'));
        $status_sampel   = $this->getValidStatusSampelValidasi($request->input('status_sampel'));
        $order           = $request->input('order', 'tanggal_divalidasi');

        $record = Sampel::selectSampel()->sampel($status_sampel);
        if ($params) {
            $record->filter($params);
        }
        $record->order($order, $order_direction);
        return SampelResource::collection($record->paginate($perpage));
    }

    public function export(Request $request)
    {
        $models          = $this->index($request);
        $status_sampel   = $this->getValidStatusSampelValidasi($request->input('status_sampel'));
        $namaFile        = $this->getNameFileValidasi($status_sampel);
        return Excel::download(new ValidasiExport($models, $this->getNomorUrut($request)), $namaFile);
    }
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

    public function getValidator()
    {
        return response()->json(['data' => Validator::whereIsActive(true)->get()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sampel       $sampel
     *
     * @return \Illuminate\Http\Response
     */
    public function updateToValidate(ValidasiSampelRequest $request, Sampel $sampel)
    {
        DB::beginTransaction();
        try {
            $this->checkValidNomorRegister($sampel);
            $pemeriksaanSampel = PemeriksaanSampel::find($request->input('last_pemeriksaan_id'));
            $pemeriksaanSampel->catatan_pemeriksaan = $request->input('catatan_pemeriksaan');
            $pemeriksaanSampel->save();
            $this->sendSampleValidPositif($sampel);
            $this->updateSampel($sampel, $request, $pemeriksaanSampel);
            DB::commit();
            return response()->json(['message' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkValidate(ValidasiSampelBulkRequest $request)
    {
        DB::beginTransaction();
        try {
            Sampel::whereIn('id', $request->input('sampels'))
                  ->chunk(100, function ($sampels) use ($request) {
                    foreach ($sampels as $sampel) {
                        $this->checkValidNomorRegister($sampel);
                        $this->sendSampleValidPositif($sampel);
                        $this->updateSampel($sampel, $request, $sampel);
                    }
                  });
            DB::commit();
            return response()->json(['message' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function rejectSample(Sampel $sampel)
    {
        $sampel->updateState('pcr_sample_analyzed', [
            'user_id' => Auth::user()->id,
            'metadata' => $sampel,
            'description' => 'Verifikasi ditolak (mohon ditinjau kembali)',
        ]);
        return response()->json(['message' => 'success']);
    }

    private function updateSampel($sampel, $request, $metadata)
    {
        $sampel->addLog([
            'user_id' => $request->user()->id,
            'metadata' => $metadata,
            'description' => 'Hasil pemeriksaan tervalidasi',
        ], 'sample_valid');
        $sampel->update([
            'validator_id' => $request->input('validator'),
            'sampel_status' => 'sample_valid',
            'waktu_sample_valid' => now(),
        ]);
    }
}
