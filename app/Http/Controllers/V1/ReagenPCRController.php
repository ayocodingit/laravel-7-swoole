<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReagenPCRRequest;
use App\Http\Resources\ReagenPCRResource;
use App\Models\ReagenPCR;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReagenPCRController extends Controller
{
    use PaginateTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search          = $request->get('search', false);
        $order           = $request->get('order', 'nama');
        $perpage         = $this->getValidPerpage($request->get('perpage'));
        $order_direction = $request->get('order_direction', 'asc');

        $record = ReagenPCR::query();
        $record->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nama', 'ilike', '%' . $search . '%');
                if (filter_var($search, FILTER_VALIDATE_INT)) {
                    $query->orWhere('ct_normal', (int) $search);
                }
            });
        });
        $isAllowedOrder = ['nama', 'ct_normal'];
        $record->when(in_array($order, $isAllowedOrder), function ($query) use ($order, $order_direction) {
            $query->orderBy($order, $this->getValidOrderDirection($order_direction));
        });
        return ReagenPCRResource::collection($record->paginate($perpage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReagenPCRRequest $request)
    {
        ReagenPCR::create($request->validated());
        return response()->json(['message' => 'CREATED'], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ReagenPCR $reagen_pcr)
    {
        return response()->json(['result' => $reagen_pcr]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReagenPCRRequest $request, ReagenPCR $reagen_pcr)
    {
        $reagen_pcr->fill($request->validated())->save();
        return response()->json(['message' => 'UPDATED']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReagenPCR $reagen_pcr)
    {
        $reagen_pcr->delete();
        return response()->json(['message' => 'DELETED']);
    }
}
