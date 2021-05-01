<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\SampelPerDomisiliResource;
use App\Models\PasienRegister;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SampelPerDomisili extends Controller
{
    use PaginateTrait;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $order           = $request->get('order', 'domisili');
        $perpage         = $request->get('perpage', 20);
        $order_direction = $request->get('order_direction', 'asc');

        $models = PasienRegister::joinRegisterFromPasienRegister()
            ->joinPasien()
            ->joinFasyankes()
            ->joinWilayah()
            ->joinSampel()
            ->select(DB::raw('upper(kota.nama) as domisili'), DB::raw('count(*) as jumlah'))
            ->groupBy('kota.nama');
        $models->orderBy($this->getValidOrder($order), $this->getValidOrderDirection($order_direction));
        return SampelPerDomisiliResource::collection($models->paginate($this->getValidPerpage($perpage)));
    }

    private function getValidOrder($order)
    {
        if (!in_array($order, ['domisili', 'jumlah'])) {
            $order = 'domisili';
        }
        return $order;
    }
}
