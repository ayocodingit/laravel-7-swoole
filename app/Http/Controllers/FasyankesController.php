<?php

namespace App\Http\Controllers;

use App\Http\Requests\FasyankesRequest;
use App\Http\Resources\FasyankesResource;
use App\Models\Fasyankes;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FasyankesController extends Controller
{
    use PaginateTrait;

    public function index(Request $request)
    {
        $perpage         = $this->getValidPerpage($request->input('perpage'));
        $order_direction = $this->getValidOrderDirection($request->input('order_direction'), 'asc');
        $order           = $request->input('order', 'nama');
        $search          = $request->input('search', false);

        $record = Fasyankes::select('fasyankes.*')->joinKota();

        if ($search) {
            $record->search($search);
        }
        $record->order($order, $order_direction);
        return FasyankesResource::collection($record->paginate($perpage));
    }

    public function store(FasyankesRequest $request)
    {
        Fasyankes::create($request->validated());
        return response()->json(['message' => 'CREATED'], Response::HTTP_CREATED);
    }

    public function destroy(Fasyankes $dinke)
    {
        abort_if(
            $dinke->register()->exists(),
            Response::HTTP_BAD_REQUEST,
            'Tidak dapat hapus, karena data sedang digunakan'
        );
        $dinke->delete();
        return response()->json(['message' => 'DELETED']);
    }

    public function update(FasyankesRequest $request, Fasyankes $dinke)
    {
        $dinke->fill($request->validated())->save();
        return response()->json(['message' => 'UPDATED']);
    }

    public function show(Fasyankes $dinke)
    {
        return response()->json(['result' => $dinke]);
    }
}
