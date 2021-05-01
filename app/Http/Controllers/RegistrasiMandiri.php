<?php

namespace App\Http\Controllers;

use App\Exports\MandiriExport;
use App\Exports\RujukanExport;
use App\Http\Resources\RegistrasiMandiriResource;
use App\Models\PasienRegister;
use App\Traits\ExportMappingTrait;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RegistrasiMandiri extends Controller
{
    use ExportMappingTrait;
    use PaginateTrait;

    public function getData(Request $request)
    {
        $params          = $this->getValidParams($request);
        $order           = $request->input('order', 'tgl_input');
        $order_direction = $request->input('order_direction', 'asc');
        $perpage         = $request->input('perpage', 20);

        $models = PasienRegister::selectCustom()
                                    ->joinRegisterFromPasienRegister()
                                    ->joinPasien()
                                    ->joinFasyankes()
                                    ->joinSampel()
                                    ->joinStatusSampel()
                                    ->joinWilayah();

        if ($params) {
            $models->filter($params);
        }
        $models->order($order, $this->getValidOrderDirection($order_direction));
        return RegistrasiMandiriResource::collection($models->paginate($this->getValidPerpage($perpage)));
    }

    public function exportMandiri(Request $request)
    {
        $models = $this->getData($request);
        $namaFile = 'Registrasi-Mandiri-' . time() . '.xlsx';
        return Excel::download(new MandiriExport($models, $this->getNomorUrut($request)), $namaFile);
    }

    public function exportRujukan(Request $request)
    {
        $models = $this->getData($request);
        $namaFile = 'Registrasi-Rujukan-' . time() . '.xlsx';
        return Excel::download(new RujukanExport($models, $this->getNomorUrut($request)), $namaFile);
    }
}
