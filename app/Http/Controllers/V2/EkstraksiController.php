<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\EkstraksiResource;
use App\Models\Sampel;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class EkstraksiController extends Controller
{
    use PaginateTrait;

    public function getData(Request $request)
    {
        $order           = $request->input('order', 'nomor_sampel');
        $order_direction = $request->input('order_direction', 'asc');
        $params          = $this->getValidParams($request);
        $perpage         = $request->input('perpage', 20);

        $sampel = Sampel::selectEkstraksi()->queryEkstraksiPcr();

        if ($params) {
            $sampel->filter($params, 'ekstraksi');
        }
        $sampel->order($order, $this->getValidOrderDirection($order_direction), 'ekstraksi');
        return EkstraksiResource::collection($sampel->paginate($this->getValidPerpage($perpage)));
    }
}
