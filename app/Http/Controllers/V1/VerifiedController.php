<?php

namespace App\Http\Controllers\V1;

use App\Exports\VerifikasiExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\SampelResource;
use App\Models\Sampel;
use App\Traits\ExportMappingTrait;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VerifiedController extends Controller
{
    use ExportMappingTrait;
    use PaginateTrait;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params          = $this->getValidParams($request);
        $perpage         = $this->getValidPerpage($request->input('perpage'));
        $order_direction = $this->getValidOrderDirection($request->input('order_direction'));
        $order           = $request->input('order', 'tanggal_diverikasi');

        $record = Sampel::selectSampel()->sampel(['sample_verified', 'sample_valid']);

        if ($params) {
            $record->filter($params);
        }
        $record->order($order, $order_direction);
        return SampelResource::collection($record->paginate($perpage));
    }

    public function export(Request $request)
    {
        $models = $this->index($request);
        $namaFile = 'Terverifikasi-' . time() . '.xlsx';
        return Excel::download(new VerifikasiExport($models, $this->getNomorUrut($request)), $namaFile);
    }
}
