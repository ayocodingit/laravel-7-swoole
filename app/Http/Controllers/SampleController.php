<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Models\Sampel;
use App\Models\PengambilanSampel;
use App\Traits\SampelTrait;
use App\Traits\PaginateTrait;
use App\Http\Requests\SampelRequest;
use App\Http\Resources\ShowSampelResource;
use App\Http\Resources\SampelResource;

class SampleController extends Controller
{
    use SampelTrait;
    use PaginateTrait;

    public function getData(Request $request)
    {
        $params          = $this->getValidParams($request);
        $perpage         = $this->getValidPerpage($request->input('perpage'));
        $order_direction = $this->getValidOrderDirection($request->input('order_direction'));
        $search          = $request->input('search', false);
        $order           = $request->input('order', 'tgl_input_sampel');

        $record          = Sampel::selectSampelAdmin()->sampelIsFromMigration();
        $record          = $this->searchAdminSampel($record, $search);

        if ($params) {
            $record->filter($params, 'sampel_admin');
        }
        $record->order($order, $order_direction, 'adminSampel');
        return SampelResource::collection($record->paginate($perpage));
    }

    public function store(SampelRequest $request)
    {
        DB::beginTransaction();
        try {
            $pengambilanSampel = new pengambilanSampel();
            $pengambilanSampel->fill($this->saveSampelRequest($request));
            $pengambilanSampel->save();
            $storeSampel = [
                'pengambilan_sampel_id' => $pengambilanSampel->id,
                'waktu_waiting_sample' => now(),
                'sampel_status' => 'sample_taken',
            ];
            $sampel = new Sampel();
            $sampel->fill($this->saveSampelRequest($request) + $storeSampel);
            $sampel->save();
            $this->updateState($sampel, 'sample_taken', $sampel, 'Data Sampel Teregistrasi');
            DB::commit();
            return response()->json(['message' => 'Berhasil menambahkan sampel'], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function show($nomor_sampel)
    {
        $model = Sampel::where('sampel.nomor_sampel', 'ilike', $nomor_sampel)
            ->joinRegisterSampel()
            ->joinPengambilanSampel()
            ->selectChumberDetailSampel()
            ->firstOrFail();
        return new ShowSampelResource($model);
    }

    public function destroy(Sampel $sampel)
    {
        $sampel->delete();
        return response()->json(['message' => 'Hapus data berhasil']);
    }

    public function update(SampelRequest $request, Sampel $sampel)
    {
        DB::beginTransaction();
        try {
            $sampel->fill($this->saveSampelRequest($request))->save();
            $pengambilanSampel = PengambilanSampel::firstOrNew(['id' => $sampel->pengambilan_sampel_id]);
            $pengambilanSampel->fill($this->saveSampelRequest($request))->save();
            if ($sampel->sampel_status == 'waiting_sample') {
                $this->updateState($sampel, 'sample_taken', $sampel, 'Data Sampel Teregistrasi');
            }
            DB::commit();
            return response()->json(['message' => 'Berhasil menambahkan sampel'], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function getSampel($nomor_sampel)
    {
        $sampel = Sampel::select('nomor_sampel', 'sampel.id')
                    ->where('nomor_sampel', 'ilike', $nomor_sampel)
                    ->get();
        return response()->json($sampel);
    }
}
