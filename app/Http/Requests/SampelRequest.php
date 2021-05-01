<?php

namespace App\Http\Requests;

use App\Models\Sampel;
use App\Rules\UniqueSampel;
use App\Enums\SumberSampelEnum;
use App\Enums\JenisSampelEnum;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Enum\Laravel\Rules\EnumValueRule;

class SampelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $pen_sampel_sumber = $this->get('pen_sampel_sumber');
        $regexRule = 'regex:/^' . Sampel::NUMBER_FORMAT_RUJUKAN . '$/';
        if ($pen_sampel_sumber == SumberSampelEnum::mandiri()->getValue()) {
            $regexRule = 'regex:/^' . Sampel::NUMBER_FORMAT_MANDIRI . '$/';
        }

        return [
            'pen_sampel_sumber' => ['required', new EnumValueRule(SumberSampelEnum::class)],
            'sam_jenis_sampel' => 'required|numeric|exists:jenis_sampel,id',
            'nomorsampel' => ['required', $regexRule, new UniqueSampel(optional($this->sampel)->id)],
            'petugas_pengambil' => 'required',
            'vtm' => 'required',
            'pen_catatan' => 'nullable',
            'pen_nik' => 'nullable',
            'pen_noreg' => 'nullable',
            'pen_penerima_sampel' => 'nullable',
            'pukulsampel' => 'required|date_format:H:i',
            'tanggalsampel' => 'required|date',
            'sam_namadiluarjenis' => 'required_if:sam_jenis_sampel,' . JenisSampelEnum::luarJenis()->getIndex()
        ];
    }
}
