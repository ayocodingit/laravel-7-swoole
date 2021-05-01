<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifikasiUpdateToVerifiedRequest extends FormRequest
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
            'kesimpulan_pemeriksaan' => 'required|in:positif,negatif,invalid,inkonklusif',
            'catatan_pemeriksaan' => 'nullable|max:255',
            'last_pemeriksaan_id' => 'required|exists:pemeriksaansampel,id',
            'hasil_deteksi' => 'required|array',
            'hasil_deteksi.*.target_gen' => 'required',
            'hasil_deteksi.*.ct_value' => 'required',
        ];
    }
}
