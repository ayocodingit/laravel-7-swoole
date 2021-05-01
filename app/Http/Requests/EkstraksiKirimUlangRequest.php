<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EkstraksiKirimUlangRequest extends FormRequest
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
            'operator_ekstraksi' => 'required',
            'tanggal_mulai_ekstraksi' => 'required',
            'jam_mulai_ekstraksi' => 'required',
            'metode_ekstraksi' => 'required',
            'nama_kit_ekstraksi' => 'required',
            'tanggal_pengiriman_rna' => 'required',
            'jam_pengiriman_rna' => 'required',
            'nama_pengirim_rna' => 'required',
            'catatan_pengiriman' => 'nullable',
            'lab_pcr_id' => 'required|exists:lab_pcr,id',
            'lab_pcr_nama' => 'required_if:lab_pcr_id,999999',
            'samples.*.nomor_sampel' => 'required|exists:sampel,nomor_sampel,deleted_at,NULL',
        ];
    }
}
