<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidasiSampelBulkRequest extends FormRequest
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
            'sampels' => 'required|array',
            'validator' => 'required|exists:validator,id',
            'sampels.*' => 'required|exists:sampel,id,deleted_at,NULL'
        ];
    }
}
