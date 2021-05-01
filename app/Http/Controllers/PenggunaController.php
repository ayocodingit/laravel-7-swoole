<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests\PenggunaRequest;
use Illuminate\Http\Response;
use App\Traits\PaginateTrait;
use App\Http\Resources\PenggunaResource;

class PenggunaController extends Controller
{
    use PaginateTrait;

    public function index(Request $request)
    {
        $search          = $request->input('search', false);
        $order           = $request->input('order', 'name');
        $perpage         = $this->getValidPerpage($request->input('perpage'));
        $order_direction = $this->getValidOrderDirection($request->input('order_direction'), 'asc');

        $record = User::with(['roles','lab_pcr','validator']);
        if ($search) {
            $record->search($search);
        }
        $record->order($order, $order_direction);
        return PenggunaResource::collection($record->paginate($perpage));
    }

    public function store(PenggunaRequest $request)
    {
        User::create($request->validated());
        return response()->json(['message' => 'CREATED'], Response::HTTP_CREATED);
    }

    public function destroy(User $pengguna)
    {
        abort_if(
            $pengguna->sampel()->exists(),
            Response::HTTP_BAD_REQUEST,
            'Tidak dapat hapus, karena data sedang digunakan'
        );
        $pengguna->delete();
        return response()->json(['message' => 'DELETED']);
    }

    public function update(PenggunaRequest $request, User $pengguna)
    {
        $pengguna->fill($request->except('password'));
        if ($request->input('password')) {
            $pengguna->password = $request->input('password');
        }
        $pengguna->save();
        return response()->json(['message' => 'UPDATED']);
    }

    public function show(User $pengguna)
    {
        return response()->json(['result' => $pengguna]);
    }
}
