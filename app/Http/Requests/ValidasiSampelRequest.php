<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidasiSampelRequest extends FormRequest
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
            'validator' => 'required|exists:validator,id',
            'catatan_pemeriksaan' => 'nullable|max:255',
            'last_pemeriksaan_id' => 'required|exists:pemeriksaansampel,id',
        ];
    }
}
