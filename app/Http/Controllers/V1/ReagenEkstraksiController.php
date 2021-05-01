<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReagenEkstraksiRequest;
use App\Http\Resources\ReagenEkstraksiResource;
use App\Models\ReagenEkstraksi;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReagenEkstraksiController extends Controller
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
        $order_direction = $request->input('order_direction', 'asc');

        $models = ReagenEkstraksi::query();
        $models->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nama', 'ilike', '%' . $search . '%')
                    ->orWhere('metode_ekstraksi', $search);
            });
        });
        $models->when(in_array($order, ['nama', 'metode_ekstraksi']), function ($query) use ($order, $order_direction) {
            $query->orderBy($order, $this->getValidOrderDirection($order_direction));
        });
        return ReagenEkstraksiResource::collection($models->paginate($perpage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReagenEkstraksiRequest $request)
    {
        ReagenEkstraksi::create($request->validated());
        return response()->json(['message' => 'CREATED'], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ReagenEkstraksi $reagenEkstraksi)
    {
        return response()->json(['result' => $reagenEkstraksi]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReagenEkstraksiRequest $request, ReagenEkstraksi $reagen_ekstraksi)
    {
        $reagen_ekstraksi->fill($request->validated())->save();
        return response()->json(['message' => 'UPDATED']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReagenEkstraksi $reagenEkstraksi)
    {
        $reagenEkstraksi->delete();
        return response()->json(['message' => 'DELETED']);
    }
}
