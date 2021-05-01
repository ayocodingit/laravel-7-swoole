<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\PelacakanSampelResource;
use App\Models\Sampel;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class PelacakanSampelController extends Controller
{
    use PaginateTrait;

    public function index(Request $request)
    {
        $params          = $this->getValidParams($request);
        $order           = $request->input('order', 'tanggal_taken');
        $perpage         = $request->input('perpage', 20);
        $order_direction = $request->input('order_direction', 'desc');
        $isFilter        = $request->input('isFilter', false);

        $models = Sampel::with('validator')
            ->querySampelDefault()
            ->joinValidator()
            ->selectSampel();

        if ($params) {
            $models->filter($params);
        }
        if ($order) {
            $models->order($order, $this->getValidOrderDirection($order_direction))
                    ->orderValidator($order, $this->getValidOrderDirection($order_direction));
        }
        if ($isFilter) {
            return PelacakanSampelResource::collection($models->paginate($this->getValidPerpage($perpage)));
        }
        //response when not filtering
        return response()->json(['data' => [], 'count' => 0]);
    }
}
