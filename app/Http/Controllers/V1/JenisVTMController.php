<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\JenisVTMRequest;
use App\Http\Resources\JenisVTMResource;
use App\Models\JenisVTM;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JenisVTMController extends Controller
{
    use PaginateTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search          = $request->input('search', false);
        $order           = $request->input('order', 'nama');
        $perpage         = $this->getValidPerpage($request->input('perpage'));
        $order_direction = $this->getValidOrderDirection($request->input('order_direction'), 'asc');

        $record = JenisVTM::query();
        $record->when($search, function ($query) use ($search) {
            $query->where('nama', 'ilike', '%' . $search . '%');
        });
        $record->when($order == 'nama', function ($query) use ($order, $order_direction) {
            $query->orderBy($order, $order_direction);
        });
        return JenisVTMResource::collection($record->paginate($perpage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JenisVTMRequest $request)
    {
        JenisVTM::create($request->validated());
        return response()->json(['message' => 'CREATED'], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(JenisVTM $jenis_vtm)
    {
        return response()->json(['result' => $jenis_vtm]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(JenisVTMRequest $request, JenisVTM $jenis_vtm)
    {
        $jenis_vtm->fill($request->validated())->save();
        return response()->json(['message' => 'UPDATED']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(JenisVTM $jenis_vtm)
    {
        $jenis_vtm->delete();
        return response()->json(['message' => 'DELETED']);
    }
}
