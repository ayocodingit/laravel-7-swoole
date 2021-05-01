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

class NotVerifiedController extends Controller
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
        $order           = $request->input('order', 'tanggal_diverikasi');
        $perpage         = $request->input('perpage', 20);
        $order_direction = $request->input('order_direction', 'desc');

        $record = Sampel::sampel(['pcr_sample_analyzed', 'inkonklusif', 'swab_ulang'])
                            ->selectSampel()
                            ->where('pemeriksaansampel.kesimpulan_pemeriksaan', '!=', 'invalid');

        if ($params) {
            $record->filter($params);
        }
        $record->order($order, $this->getValidOrderDirection($order_direction));
        return SampelResource::collection($record->paginate($this->getValidPerpage($perpage)));
    }

    public function export(Request $request)
    {
        $models = $this->index($request);
        $namaFile = 'Belum diverifikasi-' . time() . '.xlsx';
        return Excel::download(new VerifikasiExport($models, $this->getNomorUrut($request)), $namaFile);
    }
}
