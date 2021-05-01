<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sampel;
use App\Http\Resources\PcrResource;
use App\Traits\PaginateTrait;

class PCRController extends Controller
{
    use PaginateTrait;

    public function index(Request $request)
    {
        $params          = $this->getValidParams($request);
        $order           = $request->input('order', 'nomor_sampel');
        $perpage         = $this->getValidPerpage($request->input('perpage'));
        $order_direction = $request->input('order_direction', 'asc');

        $record = Sampel::selectPcr()->queryEkstraksiPcr();

        if ($params) {
            $record->filter($params, 'pcr');
        }
        $record->order($order, $this->getValidOrderDirection($order_direction), 'pcr');
        return PcrResource::collection($record->paginate($perpage));
    }
}
