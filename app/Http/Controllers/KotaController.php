<?php

namespace App\Http\Controllers;

use App\Http\Requests\KotaRequest;
use App\Http\Resources\KotaResource;
use App\Models\Kota;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KotaController extends Controller
{
    use PaginateTrait;

    public function index(Request $request)
    {
        $search          = $request->input('search', false);
        $order           = $request->input('order', 'nama');
        $perpage         = $this->getValidPerpage($request->input('perpage'));
        $order_direction = $this->getValidOrderDirection($request->input('order_direction'), 'asc');

        $record = Kota::select('kota.*')->joinProvinsi();
        if ($search) {
            $record->search($search);
        }
        $record->order($order, $order_direction);
        return KotaResource::collection($record->paginate($perpage));
    }

    public function store(KotaRequest $request)
    {
        Kota::create($request->validated());
        return response()->json(['message' => 'CREATED'], Response::HTTP_CREATED);
    }

    public function destroy(Kota $kotum)
    {
        $kotum->delete();
        return response()->json(['message' => 'DELETED']);
    }

    public function update(KotaRequest $request, Kota $kotum)
    {
        $kotum->fill($request->validated())->save();
        return response()->json(['message' => 'UPDATED']);
    }

    public function show(Kota $kotum)
    {
        return response()->json(['result' => $kotum]);
    }
}
