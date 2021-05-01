<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Sampel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CekNomorSampelController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            $this->setNotValidResult($validator->errors()->first('nomor_sampel'));
            return response()->json($this->result);
        }
        $sampel = Sampel::where('nomor_sampel', 'ilike', $request->input('nomor_sampel'))->first();
        if ($sampel->sampel_status != $request->input('sampel_status')) {
            $tahapan = $sampel->status ? $sampel->status->deskripsi : $sampel->sampel_status;
            $this->setNotValidResult("Status sampel sudah pada tahap $tahapan");
        }

        if (!empty($request->user()->lab_pcr_id) && $sampel->lab_pcr_id != $request->user()->lab_pcr_id) {
            $this->setNotValidResult(
                "Sampel ini tidak diarahkan ke Lab Anda, namun ke Lab PCR di {$sampel->lab_pcr_nama}.
                <br>Silakan hubungi Admin Ekstraksi (Labkesda Jabar)"
            );
        }
        return response()->json($this->result);
    }


    private $result = [
        'valid' => true,
        'error' => ''
    ];

    private function setNotValidResult($error)
    {
        $this->result['valid'] = false;
        $this->result['error'] = $error;
    }

    private function rules(): array
    {
        return [
            'nomor_sampel' => [
                'required',
                'regex:/^' . Sampel::NUMBER_FORMAT . '$/',
                'exists:sampel,nomor_sampel,deleted_at,NULL'
            ],
        ];
    }
}
