<?php

namespace App\Http\Requests;

use App\Enums\MetodeEkstraksiEnum;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Enum\Laravel\Rules\EnumValueRule;

class TerimaEkstraksiRequest extends FormRequest
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
        return [
            'tanggal_penerimaan_sampel' => 'required',
            'tanggal_mulai_ekstraksi' => 'required',
            'jam_penerimaan_sampel' => 'required',
            'jam_mulai_ekstraksi' => 'required',
            'petugas_penerima_sampel' => 'required',
            'operator_ekstraksi' => 'required',
            'metode_ekstraksi' => [
                'required',
                new EnumValueRule(MetodeEkstraksiEnum::class),
            ],
            'nama_kit_ekstraksi' => 'required_if:metode_ekstraksi,' . MetodeEkstraksiEnum::Manual()->getValue(),
            'alat_ekstraksi' => 'required_if:metode_ekstraksi,' . MetodeEkstraksiEnum::Otomatis()->getValue(),
            'samples' => 'required|array',
            'samples.*.nomor_sampel' => 'required|exists:sampel,nomor_sampel,deleted_at,NULL',
            'catatan_penerimaan' => 'nullable'
        ];
    }
}
