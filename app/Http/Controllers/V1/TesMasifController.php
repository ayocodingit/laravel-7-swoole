<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TesMasifResource;
use App\Models\Pasien;
use App\Models\PengambilanSampel;
use App\Models\Register;
use App\Models\Sampel;
use App\Models\TesMasif;
use App\Traits\PaginateTrait;
use App\Traits\RegisterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TesMasifController extends Controller
{
    use RegisterTrait;
    use PaginateTrait;

    public function index(Request $request)
    {
        $params             = $this->getValidParams($request);
        $order_direction    = $this->getValidOrderDirection($request->input('order_direction'), 'asc');
        $perpage            = $this->getValidPerpage($request->input('perpage'));
        $order              = $request->input('order', 'nama_pasien');
        $status             = $request->input('status', 'waiting');

        $models = TesMasif::with('kota:id,nama')
                            ->select('tes_masif.*')
                            ->joinKota()
                            ->status($status)
                            ->jenisRegistrasiMandiri();
        if ($params) {
            $models->filter($params);
        }
        $models->order($order, $order_direction);
        return TesMasifResource::collection($models->paginate($perpage));
    }

    public function bulk(Request $request)
    {
        DB::beginTransaction();
        try {
            TesMasif::whereIn('id', $request->input('id'))
                    ->where('available', true)
                    ->chunk(100, function ($dataTesMasif) {
                        foreach ($dataTesMasif->toArray() as $tes_masif) {
                            $tes_masif = $this->mappingRecordFromTesMasif($tes_masif);
                            $this->addRecordFromTesMasif($tes_masif);
                            TesMasif::find($tes_masif['id'])->update(['available' => false]);
                        }
                    });
            DB::commit();
            return response()->json(['message' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function addRecordFromTesMasif($tes_masif)
    {
        $register = Register::create($tes_masif);
        $pasien = Pasien::create($tes_masif);
        $register->pasiens()->attach($pasien);
        $pengambilan_sampel = PengambilanSampel::create($tes_masif);
        $tes_masif['register_id'] = $register->id;
        $tes_masif['nomor_register'] = $register->nomor_register;
        $tes_masif['pengambilan_sampel_id'] = $pengambilan_sampel->id;
        Sampel::create($tes_masif);
    }

    private function mappingRecordFromTesMasif($tes_masif)
    {
        $tes_masif['nama_lengkap'] = $tes_masif['nama_pasien'];
        $tes_masif['alamat_lengkap'] = $tes_masif['alamat'];
        $tes_masif['nomor_register'] = $this->generateNomorRegister(null, $tes_masif['jenis_registrasi']);
        $tes_masif['status'] = $tes_masif['kriteria'];
        $tes_masif['catatan'] = $tes_masif['keterangan'];
        $tes_masif['waktu_waiting_sample'] = now();
        $tes_masif['sumber_pasien'] = $tes_masif['kategori'];
        $tes_masif['creator_user_id'] = Auth::user()->id;
        $tes_masif['sampel_status'] = 'waiting_sample';
        $tes_masif['register_uuid'] = Str::uuid();
        $tes_masif['kewarganegaraan'] = $tes_masif['kewarganeraan'];
        return $tes_masif;
    }
}
